<table class="order_report">
  <thead>
    <tr>
    	<td colspan="4" style="text-align:left;font-size: 12px;height: 20px;">ORDER REPORT , {{date('M d Y')}} </td>
    </tr>
    
    <tr style="background-color:#00B200">
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 5px;border-top: 5px solid #000000;border: 1px solid #000000;">No.</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 5px;border-top: 5px solid #000000;border: 1px solid #000000;">OrderId</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 20px;border-top: 5px solid #000000;border:1px solid #0000000;">Customer Name</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 20px;border-top: 5px solid #000000;border:1px solid #0000000;">Customer Email</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 12px;border-top: 5px solid #000000;border:1px solid #0000000;">Order Date</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 12px;border-top: 5px solid #000000;border:1px solid #0000000;">Order Status</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 15px;border-top: 5px solid #000000;border:1px solid #0000000;">Total Amount</th>
    </tr>
  </thead>
  <tbody>
       @php                        

       @endphp
       @foreach ($orders as $key=>$item)
          <tr>
            @php                
            @endphp
            <td style="text-align:center;border: 1px solid #000000;">{{$key+1}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{$item->id ?? ''}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{$item->customer->first_name ?? ''}} {{$item->customer->last_name ?? ''}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{ $item->customer->email ?? ''}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{ $item->created_at->format('d-m-Y') ?? '' }}</td>
            <td style="text-align:center;border: 1px solid #000000;">
            	@if ($item->order_status === Null)
                    Processing
                @else
                    @foreach (App\Models\Order::ORDER_STATUS as $key=>$label)
                       @if($item->order_status == $key)
                            {{ $label ?? ''}}
                       @endif
                    @endforeach
                @endif
            </td>
            <td style="text-align:center;border: 1px solid #000000;">
                @php
                    $toal_amount = 0;
                    $sub_total = 0;
                    $grand_total = 0;
                    $discount_price = 0;
                    $dics_percent = 0;
                    $dics_amount = 0;
                    $cal_percent = 0;
                    $coupon_amt = 0;
                    $customer_percent = 0;
                    $amt = 0;
                @endphp
                @foreach ($item->orderItems as $label)
                    @php
                        $dics_percent = $label->discount_percent;                                            
                        $dics_amount += $label->discount_amount;
                        $discount_price  = ($label->price) - ($label->discount_percent);
                        $total_amount = $label->price;
                        $sub_total += $total_amount;
                        $cal_percent = ($sub_total / 100)* $label->discount_percent ;
                        
                        if($item->customer_type==1){
                            $customer_percent = 0;
                        }else{
                                if(!isset($item->customertype->discount_amount)){
                                    $amt = 0;
                                }else{
                                    $amt = $item->customertype->discount_amount;
                                }
                            $customer_percent = ($sub_total / 100) * $amt;
                        }

                        if($label->coupon_code == NULL){
                            $coupon_amt = 0;
                        }else{
                            $coupon_amt = $label->coupon->coupon_amount;
                        }

                        if($item->coupon_code == NULL){
                                $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent);
                            }else{
                                if($item->coupon->discount_type=="percent"){
                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + (($sub_total / 100)* $item->coupon->coupon_amount));
                                }elseif($item->coupon->discount_type=="fixed_cart"){
                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + $item->coupon->coupon_amount);
                                }
                                else{
                                    $grand_total = ($sub_total + $item->shipping_amount) - ($dics_amount + $cal_percent + $customer_percent + $item->coupon->coupon_amount);
                                }
                            }
                    @endphp        
                @endforeach

                {{$item->currency->symbol ?? '$'}} {{ number_format($grand_total, 2, '.', ',') ?? '0' }}
            </td> 
          </tr>
                  
      @endforeach
  </tbody>
</table>