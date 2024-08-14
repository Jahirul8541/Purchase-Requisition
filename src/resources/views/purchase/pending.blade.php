<x-pondit-limitless-master>
    <x-pondit-limitless-toast />
    <x-pondit-pl-card cardTitle="PENDING PURCHASE REQUISITION">
        <x-pondit-batable tableId="baTable" class="table">
            <x-slot name="thead">
                <tr>
                    <th class="text-center">SL/NO</th>
                    <th class="text-center">ACTION</th>
                    <th class="text-center" filter="true">REQ NO</th>
                    <th class="text-center" filter="true">REQ DATE</th>
                    <th class="text-center" filter="true">TOTAL TK</th>
                    <th class="text-center" filter="true">PRODUCT TITLE</th>
                    <th class="text-center" filter="true">UNIT</th>
                    <th class="text-center" filter="true">SECTION</th>
                    {{-- <th class="text-center" filter="true">Contact Person</th>
                    <th class="text-center" filter="true">Phone</th> --}}

                </tr>
            </x-slot>
            @php
                $user_id = auth()->user()->id;
                $purchase_role_id = DB::table('system_roles')
                    ->where('alias', 'purchase')
                    ->first()->id;

                $alias_data = DB::table('system_role_user')
                                    ->join('system_roles', 'system_role_user.role_id', 'system_roles.id')
                                    ->where('system_role_user.user_id', $user_id)->first();
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
                      @if (!is_null($purchase->authorized_at))
 
                            <x-pondit-pl-btn-view href="{{ route('purchase.show', $purchase->id) }}">
                                {{ __('view') }}</x-pondit-pl-btn-view>
                       @endif
                        {{-- @dd($purchase->purchase_verified_at == null) --}}
                        @if ($purchase->created_by == auth()->user()->id || auth()->user()->isSuperAdmin())
                            <x-pondit-pl-btn-edit href="{{ route('purchase.edit', $purchase->id) }}">
                                {{ __('Edit') }}
                            </x-pondit-pl-btn-edit>
                        @endif

                        @if (!is_null($purchase->purchase_user) && $purchase->accounts_user == null && auth()->user()->id == $purchase->purchase_user )
                            <x-pondit-pl-btn-edit href="{{ route('purchase.p-user-edit', $purchase->id) }}">
                                {{ __('Edit') }}
                            </x-pondit-pl-btn-edit>
                        @endif
                        @if($alias_data->alias == "super-admin")
                            <x-pondit-pl-btn icon="file" color="success"
                                href="{{ route('purchase.pdf', $purchase->id) }}" tooltip="PDF"></x-pondit-pl-btn>
                        @endif

                        <x-pondit-pl-btn icon="tv" color="success" href="{{ route('purchase.preview', $purchase->id) }}" tooltip="preview"></x-pondit-pl-btn>

                        @if ($purchase->agm_dgm_gm_user == null && $purchase->created_by == auth()->user()->id || $purchase->agm_dgm_gm_user == null && auth()->user()->isSuperAdmin())
                            <form style="display:inline" action="{{ route('purchase.delete', $purchase->id) }}"
                                method="post">
                                @csrf
                                @method('delete')
                                <x-pondit-pl-btn-delete class="btn btn-info"
                                    onClick="return confirm('Are you sure want to delete ?')" type="submit">
                                    {{ __('Delete') }}</x-pondit-pl-btn-delete>
                            </form>
                        @endif

                    </td>
                    <td>{{ $purchase->purchase_requisition_no ?? '' }}</td>
                    <td>{{ date('d-M-Y', strtotime($purchase->created_at)) }}</td>
                    <td>{{ $purchase->grand_total ?? '' }}</td>
                        @php
                        $product_item = explode(',', $purchase->item_title);
                        @endphp
                    <td align="left" data-toggle="tooltip" data-placement="top" title="{{ $purchase->item_title }}">
                        @foreach ($product_item as $key => $item)
                            @if ($key <= 1)
                                <li>{{ $item }}</li>
                            @endif
                        @endforeach
                    </td>
                    <td align="left">{{ $purchase->unit_title ?? '' }}</td>
                    <td align="left">{{ $purchase->section_title ?? '' }}</td>

                </tr>
            @endforeach
        </x-pondit-batable>
        <x-slot name="cardFooter">
            <div></div>
            <div>
                {{-- <x-pondit-pl-btn-create href="{{ route('purchase.create') }}" /> --}}
            </div>
            <div></div>
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
        <x-pondit-helper />
        <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition.js') }}"></script>
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
