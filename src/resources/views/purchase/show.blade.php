 <x-pondit-limitless-master>
    <x-pondit-limitless-toast />
    <x-pondit-pl-card cardTitle="SHOW PURCHASE REQUISITION">
            {{-- @dd($purchase_show->purchase_items); --}}
            <div class="row">
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> UNIT NAME: </b>{{ $purchase_show->unit_title }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> SECTION: </b>{{ $purchase_show->section_title }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> CONTACT PERSON: </b>{{ $purchase_show->contact_person }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> CONTACT PERSON PHONE: </b>{{ $purchase_show->contact_person_phone }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> ORDER BY: </b>{{ $purchase_show->order_by }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b>REQUISITED BY: </b>{{ $purchase_show->requisition_by_name }}
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b>DESIGNATION : </b>{{ $purchase_show->designation }} 
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <b> GRAND TOTAL : </b>{{ $purchase_show->grand_total }} 
                </div>
            </div>
            <div class="table-responsive">
                <form action="{{ route('purchase_req_for_gate_pass') }}" method="get">
                    <input type="hidden" name="purchases_req_id" value="{{ $purchase_show->id }}">
                <table id="issues_table" class="table table-bordered table-striped overflow-x:auto ">
                    <thead>
                        <tr class="bg-success text-white" align="center">
                            <th>ITEM NAME</th>
                            <th>ITEM DESCRIPTION</th>
                            <th>QTY</th>
                            <th>STOCK</th>
                            <th>APPROVE QTY</th>
                            <th>UNIT RATE</th>
                            <th>PURPOSE</th>
                            <th>TOTAL AMOUNT</th>
                            <th>REMARKS</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @foreach ($purchase_show->purchase_items as $item)
                        @php
                         $product = DB::table('products')->where('id', $item->product_id)->first();
                        @endphp
                            <tr>
                                <td>{{ $product->title }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }} {{ $item->uom_title }}</td>
                                <td>{{ $item->stock }} {{ $item->uom_title }}</td>
                                <td>{{ $item->qty_approves }} {{ $item->uom_title }}</td>
                                <td>{{ number_format($item->unit_rate, 2, '.', ',')}}</td>
                                <td>{{ $item->purpose }}</td>
                                <td>{{ number_format($item->total_amount, 2, '.', ',') }}</td>
                                <td style="color:red">{{ $item->remarks }}</td>
                                {{-- <td>
                                    <input type="checkbox" name="item_id[]" value="{{ $item->id }}">
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 float-right mt-2">
                    {{-- <button class="btn bg-success float-right " name="gate_pass" value="1" type="submit">Gate Pass</button> --}}
                </div>
                </form>
            </div>
            {{-- <div class="pt-2 row" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <a class="float-right btn bg-success " href="{{ route('purchase.index') }}">Close</a>
                </div>
            </div> --}}
        <x-slot name="cardFooter">
            <div></div>
            <div>
                <x-pondit-pl-btn-create icon="list" tooltip="List" href="{{ route('purchase.index') }}">
                    {{ __('List') }}</x-pondit-pl-btn-create>
            </div>
            <div></div>
        </x-slot>
    </x-pondit-pl-card>

    @push('css')
    @endpush
    @push('js')
        <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition.js') }}"></script>
    @endpush
</x-pondit-limitless-master>
