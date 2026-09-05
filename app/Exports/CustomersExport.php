<?php

namespace App\Exports;

use App\Models\Customers;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Customers::with('customerType')->get();
    }

    public function map($customer): array
    {
        return [
            $customer->first_name,
            $customer->last_name,
            $customer->email,
            $customer->phone,
            $customer->gender,
            $customer->date_of_birth,
        ];

    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Gender',
            'Date Of Birth',
        ];
    }
}
