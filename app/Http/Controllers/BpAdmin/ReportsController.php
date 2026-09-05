<?php

namespace App\Http\Controllers\BpAdmin;

use App\Exports\CustomerReportExport;
use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Models\Customers;
use App\Models\CustomerTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    /** Full, filterable activity log (dashboard shows only the latest few). */
    public function activityLog(Request $request)
    {
        $logNames = Activity::query()
            ->select('log_name')->distinct()->orderBy('log_name')->pluck('log_name')->filter()->values();

        $query = Activity::with('causer')->latest();
        if ($request->filled('log') && $logNames->contains($request->query('log'))) {
            $query->where('log_name', $request->query('log'));
        }
        $activities = $query->paginate(30)->appends($request->query());

        return view('bp-admin.reports.activity', compact('activities', 'logNames'));
    }

    /** Download the activity log as CSV (respecting the category filter). */
    public function activityExport(Request $request)
    {
        $query = Activity::with('causer')->latest();
        if ($request->filled('log')) {
            $query->where('log_name', $request->query('log'));
        }

        $filename = 'activity-log-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['When', 'Who', 'Category', 'Action']);
            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $a) {
                    fputcsv($out, [
                        optional($a->created_at)->toDateTimeString(),
                        optional($a->causer)->name ?? optional($a->causer)->email ?? 'System',
                        $a->log_name,
                        $a->description,
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function customerReport(Request $request)
    {

        if ($request->start_date == null && $request->to_date == null && $request->customer_types_id == null && $request->name == null) {
            $customers = [];
            // $customers = Customers::orderBy('id','desc')->get();
        } else {
            $customers = Customers::orderBy('id', 'desc');

            if ($request->name != null) {
                if ($request->has('name')) {
                    if ($request->has('name') && isset($request->name)) {
                        // $customers = $customers->where('payment_transaction_id',$request->name);
                        // echo $request->name;
                        // - care
                        $customers->orWhereRaw('first_name like ?', ['%'.$request->name.'%']);
                    }
                }
            }

            if ($request->start_date != null) {
                if ($request->has('start_date') && isset($request->start_date)) {
                    $customers = $customers->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));

                }
            }

            if ($request->to_date != null) {
                if ($request->has('to_date') && isset($request->to_date)) {
                    $customers = $customers->whereDate('created_at', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));

                }
            }

            if ($request->customer_types_id != null) {
                if ($request->has('customer_types_id')) {
                    if ($request->customer_types_id != '') {
                        $customers = $customers->where('customer_types_id', $request->customer_types_id);
                    }

                    // echo $request->order_status;
                }
            }

            $customers = $customers->get();
        }

        $customer_types = CustomerTypes::all()->pluck('name', 'id');

        return view('bp-admin.reports.customer_report', compact('customers', 'customer_types'));
    }

    public function customerReportExport(Request $request)
    {
        return Excel::download(new CustomerReportExport($request), date('d-m').'-customers.csv');
    }

    public function customerImportView()
    {
        return view('bp-admin.reports.customer_import');
    }

    public function customerImport()
    {

        Excel::import(new CustomerImport, request()->file('file'));

        return back();

    }
}
