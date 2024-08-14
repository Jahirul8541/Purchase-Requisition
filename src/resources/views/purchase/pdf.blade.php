<!DOCTYPE html>
<html>
<head>
    <title>{{ $purchaseRequisitions->purchase_requisition_no }}</title>
</head>
<style>
    body,
    h1,
    p,
    h2,
    h3,
    h4,
    h5,
    h6 {
        padding: 0;
        margin: 0px 2px 0px 2px;
        font-size: 11px;
    }
    @page {
        sheet-size: A4-L;
        margin-footer: 0px;
        header: page-header;
        footer: page-footer;
    }


    #footer-table {
        width: 100%;
        height: auto;
        display: inline-block;
    }

    #footer-table>table {
        width: 15%;
        float: left;
        height: 100%;
    }

    .title2 {
        text-align: center;
    }


    table.footer-table {
        width: 100%;
        margin-top: 6px;
    }

    .table-design table thead tr th {
        height: 120px;
        font-size: 40px
    }

    .table-design table tbody tr td {
        height: 120px;
        font-size: 40px

    }

    .table-design table tfoot tr th {
        height: 120px;
        font-size: 40px
    }
    .child-tr th{
        font-size: 30px;
        font-weight: bold;
    }
    #table-area table {
        width: 100%;
    }

    tr {
        page-break-inside: avoid;
    }

    tr:nth-child(5n+1) {
        page-break-before: always;
    }

</style>

<body>

    @php
    $query = new Btal\PurchaseRequisition\Models\PurchaseRequisitions();

    $units = DB::table('units')
                ->where('id', $purchaseRequisitions->unit_id)
                ->first();
   @endphp
    <htmlpageheader name="page-header">
        <div class="fabric" width="100%">
            <div class="title2">
                <img src="{{ asset('/vendor/BTAL/requisitionmanagement/assets/img/logo.jpeg') }}" alt="" width="450px" height="25px">
            </div>
        </div>
    </htmlpageheader>
    <div class="container">
        <p style="font-size: 1.2rem; text-align:center;">{{$units->address ?? 'Surabari kashimpur Gajipur Bangladesh'}}</p>
        <p style="letter-spacing: 2PX; font-size: 1.5rem; text-align:center;"><strong>PURCHASE REQUISITION</strong></p>
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
                            <td>REQUISITED BY:</td>
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
  


    <div class="container">
        <div id="table-area">
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
                    <td rowspan="2">SL/NO</td>
                    <td rowspan="2">ITEM</td>
                    <td rowspan="2">ITEM DESCRIPTION</td>
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
                @php
                    $grand_total = 0;
                @endphp
                @foreach ($purchaseRequisitions->items as $item)
                
                @php
                    $grand_total +=  ($item->qty_approves * $item->unit_rate);
                    $product = DB::table('products')->where('id', $item->product_id)->first();
                    $last_purchase=DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items', 'purchase_requisition_items.purchase_requisitions_id', 'purchase_requisitions.id')
                                        ->join('purchase_challan_items', 'purchase_challan_items.pur_req_item_id', 'purchase_requisition_items.id')
                                        ->where('purchase_requisition_items.product_id', $item->product_id)
                                        ->whereNotNull('purchase_requisition_items.challan_date')
                                        ->selectRaw('
                                            purchase_requisition_items.challan_date as date, 
                                            round((purchase_challan_items.challan_qty * purchase_requisition_items.unit_rate),2) as total_taka,
                                            purchase_requisitions.unit_title
                                        ')
                                        ->orderBy('purchase_requisition_items.id', 'DESC')->first();
                        // dd($last_purchase);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td align="left">{{ $product->title }}</td>
                    <td align="left">{{ $item->description }}</td>
                    <td>{{ $item->qty }} - <span>{{ $product->uom_title }}</span></td>
                    <td>{{ $product->stock_qty }} - <span>{{ $product->uom_title }}</span></td>
                    <td>{{ $item->qty_approves }} - <span>{{ $product->uom_title }}</span></td>
                    {{-- @if ($item->qty_approves !== 0.0) --}}
                        <td>{{ number_format($item->unit_rate, 2, '.', ',') }}</td>
                    {{-- @else
                        <td>0</td>
                    @endif --}}
                    <td align="right">{{number_format(ceil($item->qty_approves * $item->unit_rate), 2)}}</td>
                    <td align="left">{{ $item->purpose }}</td>
                    <td style="color: red">{{ $item->remarks }}</td>
                    <td>{{ $item->comment }}</td>
                    <td>{{ $last_purchase !== null ? date('d-M-Y', strtotime($last_purchase->date)) : 'N/A' }}</td>
                    <td>{{ $last_purchase !== null ? $last_purchase->unit_title : 'N/A' }}</td>
                    <td>{{ $last_purchase !== null ? number_format(ceil($last_purchase->total_taka), 2) : 'N/A' }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="color: red" colspan="7" align="right"><strong>GRAND TOTAL=</strong></td>
                    @if ($purchaseRequisitions->totalApprovedQty !== 0)
                        <td align="right" style="color: red"><strong>{{ number_format(ceil($grand_total), 2)}}</strong></td>
                        {{-- number_format(ceil($item->qty_approves * $item->unit_rate), 2) --}}
                    @else
                        <td style="color: red"><strong>0</strong></td>
                    @endif
                    <td colspan="7"></td>
                </tr>
            </table>
        </div>
    </div>
    
    <htmlpagefooter name="page-footer">
        <div class="container">
            <table class="footer-table" style="
                border-collapse: collapse;
                text-align: center;
                padding:0px;
                ">
                <tr>
                    <td>
                        @if (!is_null($purchaseRequisitions->created_by))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->created_by)  }}" alt="signature" width="80" height="30">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->agmDgmGm_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->agmGmDgm)  }}" alt="signature" width="80" height="30">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->purchase_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->purchase)  }}" alt="signature" width="80" height="30">
                        @endif
                    </td>
                    <td>
                        @if (!is_null($purchaseRequisitions->accounts_verified_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->accounts)  }}" alt="signature" width="80" height="30">
                        @endif
                    </td>

                    <td>
                        @if (!is_null($purchaseRequisitions->authorized_at))
                            <img src="{{ asset('/storage/signatures/'. $purchaseRequisitions->authorize)  }}" alt="signature" width="80" height="30">
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
                        <b>{{ $query->get_user($purchaseRequisitions->data->authorized)->name }}</b>  <br>
                            <span>{{$purchaseRequisitions->authorized_at}}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="fixed-bottom" style="padding-bottom: 15px">
            <div class="left-bottom" style="width: 0%;float:right">
                <p style="right: ">Head Office: House-14(1st Floor), Road-7, Sector-10, Uttara, Dhaka-1230, Bangladesh.
                </p>

            </div>
            <div class="right-bottom" style="width: 400px;float:right">
                <p style="right: ">Tel: 880 2 55092169, 55092213.</p>
            </div>
        </div>
    </htmlpagefooter>

</body>

</html>
