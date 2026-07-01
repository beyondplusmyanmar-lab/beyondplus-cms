<?php

namespace App\Exports;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CouponsExport implements FromCollection , WithMapping , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Coupon::get();
    }

    public function map($coupon) : array {
        return [
            $coupon->coupon_code,
            $coupon->coupon_description,
            $coupon->discount_type,
            $coupon->coupon_amount,
            $coupon->allow_free_shipping,
            $coupon->start_date,
            $coupon->date,
            $coupon->minimum_spend,
            $coupon->maximum_spend,
            $coupon->limit_coupon,
            $coupon->limit_item,
            $coupon->limit_user,
            $coupon->exclude_sales_item,
            $coupon->individual_use_only,
            $coupon->used_coupon,
            $coupon->status,
        ] ;
 
 
    }

    public function headings(): array
    {
        return [
            'coupon_code',
            'coupon_description',
            'discount_type',
            'coupon_amount',
            'allow_free_shipping',
            'start_date',
            'date',
            'minimum_spend',
            'maximum_spend',
            'limit_coupon',
            'limit_item',
            'limit_user',
            'exclude_sales_item',
            'individual_use_only',
            'used_coupon',
            'status',
        ];
    }
}
