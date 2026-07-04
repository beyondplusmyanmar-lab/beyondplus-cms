<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('bp-admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('bp-admin.faq.form', ['faq' => new Faq, 'mode' => 'create']);
    }

    public function store(Request $request)
    {
        Faq::create($this->validated($request));
        return redirect('bp-admin/faq')->with('success', 'FAQ added.');
    }

    public function show($id)
    {
        return redirect('bp-admin/faq/'.$id.'/edit');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('bp-admin.faq.form', ['faq' => $faq, 'mode' => 'edit']);
    }

    public function update(Request $request, $id)
    {
        Faq::findOrFail($id)->update($this->validated($request));
        return redirect('bp-admin/faq')->with('success', 'FAQ updated.');
    }

    public function destroy($id)
    {
        Faq::where('id', $id)->delete();
        return redirect('bp-admin/faq')->with('success', 'FAQ deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = (int) $request->input('sort_order', 0);
        $data['is_active']  = $request->boolean('is_active') ? 1 : 0;
        return $data;
    }
}
