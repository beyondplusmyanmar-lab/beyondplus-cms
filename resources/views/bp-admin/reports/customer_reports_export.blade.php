<table class="customer_report">
  <thead>
    <tr>
    	<td colspan="4" style="text-align:left;font-size: 12px;height: 20px;">CUSTOMER REPORT , {{date('M d Y')}} </td>
    </tr>
    
    <tr style="background-color:#00B200">
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 5px;border-top: 5px solid #000000;border: 1px solid #000000;">No.</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 20px;border-top: 5px solid #000000;border:1px solid #0000000;">Name</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 30px;border-top: 5px solid #000000;border:1px solid #0000000;">Email</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 15px;border-top: 5px solid #000000;border:1px solid #0000000;">Phone</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 15px;border-top: 5px solid #000000;border:1px solid #0000000;">Join Date</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 15px;border-top: 5px solid #000000;border:1px solid #0000000;">Customer Type</th>
        <th style="text-align:center;font-size: 12px;font-weight: bold;vertical-align: middle;height: 15px;color: #ffffff;background-color:#70ad47;width: 15px;border-top: 5px solid #000000;border:1px solid #0000000;">Date</th>
    </tr>
  </thead>
  <tbody>
       @foreach ($customers as $key=>$item)
          <tr>
            <td style="text-align:center;border: 1px solid #000000;">{{$key+1}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{ $item->first_name .' '. $item->last_name ?? '-'}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{ $item->email ?? '-'}}</td>
            <td style="text-align:center;border: 1px solid #000000;">{{ $item->phone ?? '-'}}
            </td>
            <td style="text-align:center;border: 1px solid #000000;">
              {{date('M-d-Y', strtotime($item->created_at)) ?? '-'}}
            </td>
            <td style="text-align:center;border: 1px solid #000000;">
                {{ !empty($item->customer_types_id) ? $item->customerType->name : '' ?? ''}}
            </td>
            <td>
               {{ date('d-m-Y', strtotime($item->created_at)) ?? '-'}}
            </td>
          </tr>  
      @endforeach
  </tbody>
</table>