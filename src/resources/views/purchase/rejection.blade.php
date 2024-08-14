<x-pondit-limitless-master>
    <x-pondit-limitless-toast />
    <x-pondit-pl-card cardTitle="REJECT PURCHASE REQUISITION ">
        <x-pondit-batable tableId="baTable" class="table">
            <x-slot name="thead">
                <tr>
                    <th class="text-center">SL/NOI</th>
                    <th class="text-center">ACTION</th>
                    <th class="text-center" filter="true">REQ NO</th>
                    <th class="text-center" filter="true">REQ DATE</th>
                    <th class="text-center" filter="true">TOTAL TK</th>
                    <th class="text-center" filter="true"> PRODUCT TITLE</th>
                    <th class="text-center" filter="true">UNIT</th>
                    <th class="text-center" filter="true">SECTION</th>
                </tr>
            </x-slot>
            @php
                $user_id = auth()->user()->id;

                $purchase_role_id = DB::table('system_roles')
                    ->where('alias', 'purchase')
                    ->first()->id;

            @endphp

            @foreach ($purchase_requisitions as $key => $purchase)
                <tr class="text-center clickable-tr">
                    @php
                        $it_role_id = DB::table('system_roles')
                            ->where('alias', 'it')
                            ->first()->id;
                    @endphp
                    <td>{{ $key + 1 }}</td>
                    <td class="text-center ">
                        <x-pondit-pl-btn icon="tv" color="success" href="{{ route('purchase.preview', $purchase->id) }}" tooltip="preview"></x-pondit-pl-btn>
                        {{-- @if (auth()->user()->id == $purchase->authorized)
                        <a href="{{ route('requisition.restore', $purchase->id) }}"  onClick="return confirm('Are you sure want to Restore This ?')"  class="btn btn-info btn-sm"> Restore</a>
                        @endif --}}
                    </td>
                    <td>{{ $purchase->purchase_requisition_no ?? '' }}</td>
                    <td>{{ date('d-M-Y', strtotime($purchase->created_at)) }}</td>
                    <td>{{ $purchase->grand_total ?? '' }}</td>
                        @php
                        $product_item = explode(',', $purchase->item_title);
                        @endphp
                    <td data-toggle="tooltip" data-placement="top" title="{{ $purchase->item_title }}">
                        @foreach ($product_item as $key => $item)
                            @if ($key <= 1)
                                <li>{{ $item }}</li>
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $purchase->unit_title ?? '' }}</td>
                    <td>{{ $purchase->section_title ?? '' }}</td>
                    
                </tr>
            @endforeach
        </x-pondit-batable>
        <x-slot name="cardFooter">
            <div></div>
            <div>
                {{-- <x-pondit-pl-btn-create href="{{ route('purchase.create') }}" /> --}}
            </div>
            <div>clickable</div>
        </x-slot>
    </x-pondit-pl-card>
    @push('css')
        <style>
            .table tbody > tr:hover{
                background-color: rgb(214, 228, 238);
                color: black;
            }

            .table tbody > tr > td:hover{
                background-color: rgb(246, 252, 50);
                color:rgb(0, 0, 0);
                font-weight: bold;
            }
             .clickable-tr {
                cursor: pointer;
            }
            .selected {
                background-color: rgb(121, 245, 76)!important;
            }

            .selected td:hover{
                background-color: rgb(246, 252, 50);
                color:rgb(0, 0, 0);
                font-weight: bold;
            }
        </style>
    @endpush
    @push('js')
        <script>
            $(document).ready(function () {
                $('.table').on('click', '.clickable-tr', function () {
                    $(this).closest('.table').find('.clickable-tr').removeClass('selected');
                    $(this).addClass('selected');
                });
            }); 
        </script>
    @endpush
</x-pondit-limitless-master>
