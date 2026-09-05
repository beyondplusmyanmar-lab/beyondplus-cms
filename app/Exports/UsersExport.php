<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return User::select('name', 'email', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Date',

        ];
    }
}
