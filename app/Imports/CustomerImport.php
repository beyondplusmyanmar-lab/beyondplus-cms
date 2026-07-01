<?php

namespace App\Imports;

use App\Models\Customers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        return new Customers([

            'first_name'            => $row['name'],

            'email'                 => $row['email'], 

            'phone'                 => $row['phone'], 

            // 'password' => \Hash::make($row['password']),

            'bank_acc_name'         => $row['bank_acc_name'], 

            'bank_type'             => $row['bank_acc_type'], 

            'banknum'               => $row['bank_number'], 

            'is_verified'           => 1, 

        ]);
    }
}
