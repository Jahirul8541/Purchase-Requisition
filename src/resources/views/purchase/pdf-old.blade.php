<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
        sheet-size: A4-L;
        margin-footer: 10px;
        header: page-header;
        footer: page-footer;
    }
    </style>
</head>

<body style="font-size: 11px">
    @php
    $query = new Btal\ReequisitionManagement\Models\PurchaseRequisitions();

    $units = DB::table('units')
                ->where('id', $purchaseRequisitions->unit_id)
                ->first();
   @endphp
    {{-- <htmlpageheader name="page-header"> --}}
            <table width="100%" >
                <tr>
                    <td style="text-align:center; line-height:20px;">
                        <img src="{{ asset('/vendor/BTAL/requisitionmanagement/assets/img/logo.jpeg') }}" alt="" width="450px">
                    </td>
                </tr>
            </table>
    {{-- </htmlpageheader> --}}
  
    <div>
      
        <p style="font-size: 1.2rem; text-align:center;">{{$units->address ?? 'Surabari kashimpur Gajipur Bangladesh'}}</p>
        <p style="letter-spacing: 2PX; font-size: 1.5rem; text-align:center;"><strong>PURCHASE REQUSITION</strong></p>
        <table width="100%"  cellspacing="2">
            <tr>
                <td width="55%">
                    <table width="100%">
                        <tr>
                            <td width="40%">UNIT NAME: </td>
                            <td>{{ $purchaseRequisitions->unit_title}}</td>
                        </tr>
                        <tr>
                            <td>SECTION:</td>
                            <td>{{ $purchaseRequisitions->section_title }}</td>
                        </tr>
                        <tr>
                            <td>ORDER BY: </td>
                            <td>{{ $purchaseRequisitions->order_by }}</td>
                        </tr>
                        <tr>
                            <td>REQUISITION BY NAME:</td>
                            <td>{{ $purchaseRequisitions->requisition_by_name }}</td>
                        </tr>
                        <tr>
                            <td>DESIGNATION: </td>
                            <td>{{ $purchaseRequisitions->designation }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <td>REQUISITION NO:</td>
                            <td>{{ $purchaseRequisitions->purchase_requisition_no }}</td>
                        </tr>
                        <tr>
                            <td>DATE:</td>
                            <td>{{date('d-m-Y', strtotime( $purchaseRequisitions->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 10px"><strong>NAME OF CONTACT PERSON FOR DETAILS INFORMATION:</strong></td>
                        </tr>
                        <br>
                        <tr>
                            <td>NAME:</td>
                            <td>{{ $purchaseRequisitions->contact_person }}</td>
                        </tr>
                        <tr>
                            <td>MOBILE NO:</td>
                            <td> {{ $purchaseRequisitions->contact_person_phone }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table class="main" width="100%" border="1" cellpadding="1" style="text-align: center; border-collapse: collapse">
            <tr style="background-color: rgb(182, 178, 178)">
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>8</td>
                <td>9</td>
                <td>10</td>
                <td>11</td>
                <td colspan="3">12</td>
            </tr>

            <tr>
                <td rowspan="2">SL NO</td>
                <td rowspan="2">Item</td>
                <td rowspan="2">Item DESCRIPTION</td>
                <td rowspan="2">QTY NEEDS</td>
                <td rowspan="2">STOCK</td>
                <td rowspan="2">QTY APPROVED</td>
                <td rowspan="2">UNIT RATE</td>
                <td rowspan="2">TOTAL TAKA</td>
                <td rowspan="2">USE LOCATION / PURPOSE</td>
                <td rowspan="2">REMARKS</td>
                <td rowspan="2">AUTHORIZED COMMENT</td>
                <td colspan="3">LAST PURCHASE INFORMATION</td>
            </tr>
            <tr>
                <td>DATE</td>
                <td>UNIT</td>
                <td>TAKA</td>
            </tr>
            @foreach ($purchaseRequisitions->items as $item)
            @php
                $product = DB::table('products')->where('id', $item->product_id)->first();
                $last_purchase=DB::table('purchase_requisitions')
                                    ->join('purchase_requisition_items', 'purchase_requisition_items.purchase_requisitions_id', 'purchase_requisitions.id')
                                    ->where('product_id', $item->product_id)
                                    ->whereNotNull('purchase_requisition_items.challan_date')
                                    ->selectRaw('
                                        purchase_requisition_items.challan_date as date, 
                                        round((purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate),2) as total_taka,
                                        purchase_requisitions.unit_title
                                    ')
                                    ->orderBy('purchase_requisition_items.id', 'DESC')->first();
                    // dd($last_purchase);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product_title }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->qty }} - <span>{{ $product->uom_title }}</span></td>
                <td>{{ $item->stock }} - <span>{{ $product->uom_title }}</span></td>
                <td>{{ $item->qty_approves }} - <span>{{ $product->uom_title }}</span></td>
                @if ($item->qty_approves !== 0.0)
                    <td>{{ number_format($item->unit_rate, 2, '.', ',') }}</td>
                @else
                    <td>0</td>
                @endif
                <td>{{ number_format($item->qty_approves * $item->unit_rate, 2, '.', ',')}}</td>
                <td>{{ $item->purpose }}</td>
                <td style="color: red">{{ $item->remarks }}</td>
                <td>{{ $item->comment }}</td>
                <td>{{ $last_purchase !== null ? date('d-M-Y', strtotime($last_purchase->date)) : 'N/A' }}</td>
                <td>{{ $last_purchase !== null ? $last_purchase->unit_title : 'N/A' }}</td>
                <td>{{ $last_purchase !== null ? number_format($last_purchase->total_taka, 2, '.', ',') : 'N/A' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>GRAND TOTAL:</strong></td>
                <td>
                    <strong></strong>
                </td>
                <td></td>
                <td></td>
                <td>
                    {{-- <strong>{{ $purchaseRequisitions->totalApprovedQty}}</strong> --}}
                </td>
                <td></td>
                @if ($purchaseRequisitions->totalApprovedQty !== 0)
                    <td align="center"><strong>{{ number_format($purchaseRequisitions->grand_total, 2, '.', ',')}}</strong></td>
                @else
                    <td><strong>0</strong></td>
                @endif
                <td colspan="7"></td>
            </tr>
        </table>
    </div>
    {{-- <htmlpagefooter name="page-footer"> --}}
        <div style="position: fixed; bottom:0; padding-bottom:-40px">
            
            <table width="100%" style="text-align: center;font-size:10px">
                <tr>
                    <td>
                        @if (!is_null($purchaseRequisitions->created_by))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->created_by)  }}" alt="signature" width="80" height="50">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->agmDgmGm_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->agmGmDgm)  }}" alt="signature" width="80" height="50">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->purchase_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->purchase)  }}" alt="signature" width="80" height="50">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->accounts_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->accounts)  }}" alt="signature" width="80" height="50">
                        @endif
                    </td>

                    <td>
                        @if (!is_null($purchaseRequisitions->authorized_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->authorize)  }}" alt="signature" width="80" height="50">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th width="20%"><p style="border-top:1px solid black">PREPARED BY NAME</p></th>
                    <th width="15%" style=""><p style="border-top:1px solid black">AGM/DGM/GM/ED </p></td>
                    <th width="20%" style=""><p style="border-top:1px solid black">PURCHASE </p></td>
                    <th width="10%" style=""><p style="border-top:1px solid black">ACCOUNTS </p></td>
                    <th width="15%"><p style="border-top:1px solid black">AUTHORISED</p></td>
                </tr>
                <tr>
                    <td>
                        @if (!is_null($purchaseRequisitions->created_by))
                            <b>{{ $query->get_user($purchaseRequisitions->data->created_by)->name }}</b>  <br>
                            <span>{{$purchaseRequisitions->user_fw_at}}</span>
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->agmDgmGm_verified_at))
                        <b>{{ $query->get_user($purchaseRequisitions->data->agm_dgm_gm_user)->name }}</b>  <br>
                            <span>{{$purchaseRequisitions->agm_dgm_gm_fw_at}}</span>
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->purchase_verified_at))
                        <b>{{ $query->get_user($purchaseRequisitions->data->purchase_user)->name }}</b>  <br>
                            <span>{{$purchaseRequisitions->purchase_fw_at}}</span>
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->accounts_verified_at))
                        <b>{{ $query->get_user($purchaseRequisitions->data->accounts_user)->name }}</b>  <br>
                            <span>{{$purchaseRequisitions->accounts_fw_at}}</span>
                        @endif
                    </td>
        
                    <td>
                        @if (!is_null($purchaseRequisitions->authorized_at))
                            <span>{{$purchaseRequisitions->authorized_at}}</span>
                        @endif
                    </td>
                </tr>
            </table>
            <hr>
            <table width="100%" style="font-size:12px; margin-top:10px">
                <tr>
                    <td>
                        Head Office: House-14(1st Floor), Road-7, Sector-10, Uttara, Dhaka-1230, Bangladesh.
                    </td>
                    <td>
                        Tel: 880 2 55092169, 55092213.
                    </td>
                </tr>
            </table>
        </div>
    {{-- </htmlpagefooter> --}}

</body>

</html>
