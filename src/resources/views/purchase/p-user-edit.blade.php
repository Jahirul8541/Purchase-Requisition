<x-pondit-limitless-master>
    <x-pondit-limitless-toast />
    <x-pondit-pl-card cardTitle="PURCHASE REQUISITION EDIT">
            <div class="row">
                <input type="hidden" name="purchase_edi_id" id="purchase_edi_id" class="purchase_edi_id" value="{{ $purchase_edit->id }}">
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="section"> <b> SECTION: </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->section_title }}" readonly>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="unit"> <b> UNIT: </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->unit_title }}" readonly>

                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person"> <b> CONTACT PERSON : </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->contact_person }}" readonly>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person-phone"> <b> CONTACT PERSON PHONE: </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->contact_person_phone }}" readonly>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="order-by"> <b> ORDER BY : </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->order_by }}" readonly>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="requisition-by-name"> <b> REQUISITED BY : </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->requisition_by_name }}" readonly>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="designation"> <b> DESIGNATION : </b></label>
                    <input  class="form-control"  value="{{ $purchase_edit->designation }}" readonly>
                </div>
            </div>
            <div class="table-responsive">
                <table id="purchase_table" class="table table-bordered table-striped overflow-x:auto ">
                    <thead>
                        <tr class="bg-success text-white" align="center">
                            <th width="40%">DESCRIPTION OF GOODS</th>
                            <th>QTY</th>
                            <th>UOM TITLE</th>
                            <th>PURPOSE</th>
                            <th>DESCRIPTION</th>
                            @if (!is_null($purchase_edit->purchase_user))
                            <th>PREVIOUS UNIT RATE</th>
                            <th>UNIT RATE</th>
                            <th>TOTAL</th>
                            @endif
                            <th>REMARKS</th>
                        </tr>
                    </thead>
                    
                    <tbody id="purchase_requisition_tbody">
                        @foreach ($purchase_edit->purchase_items as $item)
                        @php
                        $product = DB::table('products')->where('id', $item->product_id)->first();
                        $last_purchase=DB::table('purchase_requisitions')
                                            ->join('purchase_requisition_items', 'purchase_requisition_items.purchase_requisitions_id', 'purchase_requisitions.id')
                                            ->join('purchase_challan_items', 'purchase_challan_items.pur_req_item_id', 'purchase_requisition_items.id')
                                            ->where('purchase_requisition_items.product_id', $item->product_id)
                                            ->whereNotNull('purchase_requisition_items.challan_date')
                                            ->selectRaw('
                                                     purchase_requisition_items.challan_date as date, 
                                                     purchase_challan_items.total as total_taka, 
                                                     purchase_challan_items.unit_rate, 
                                                     purchase_requisitions.unit_title
                                            ')
                                            ->orderBy('purchase_requisition_items.id', 'DESC')
                                            ->first();
                        @endphp
                            <tr>
                                <input type="hidden" name="item_id[]" class="item_id" value="{{ $item->id }}">
                                <td title="{{ $item->product_title }}">
                                    <input  class="form-control" value="{{ $item->product_title }}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="qty_needs[]" id="qty_needs" class="form-control qty_needs text-center"
                                        value="{{   $item->qty    }}">
                                </td>
                                <td align="center">{{$product->uom_title}}</td>
                                <td>
                                    <input  class="form-control"  value="{{ $item->purpose }}" readonly>
                                </td>
                                <td>
                                    <input  class="form-control"  value="{{ $item->description }}" readonly>
                                </td>
                                <td align="center">{{ $last_purchase != null ? $last_purchase->unit_rate : 0}}</td>
                                @if (!is_null($purchase_edit->purchase_user))
                                <td>
                                    <input type="text" class="form-control unit_rate text-right" id="unit-rate" name="unit_rate[]" value="{{ $item->unit_rate }}">
                                </td>
                                
                                <td >
                                    <input type="text" class="form-control total_amount text-right" name="total_amount[]"
                                        id="total-amount" value="{{   $item->total_amount    }}" readonly>
                                </td>
                                @endif
                                <td>
                                    <input  class="form-control"  value="{{ $item->remarks }}" readonly>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <td colspan="6"><strong>GrandTotal</strong> </td>
                        <td align="right">
                            <b id="grandtotal">{{ $purchase_edit->grand_total }}</b>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="pt-2 row" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    {{-- <button type="button" class="add_item  btn float-left btn-sm mr-5 bg-success mb-2">
                        <i class="fa fa-plus"></i>
                        ADD NEW ITEM
                    </button> --}}
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <a class="float-right btn bg-success update_data" type="submit">Update</a>
                </div>
            </div>
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
        <x-pondit-helper />
        {{-- <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition-edit.js') }}"></script> --}}

     <script>
        (function ($) {
        $(document).ready(function () {
                unit_select();
                product_select();
                qty_calculate();

                $(document).on("click", ".add_item", add_new_item);
                $(document).on("click", ".remove_item", removed_items_row);
                $(document).on("click", ".update_data", update);
                $(document).on("click", ".approve_btn", get_purchases_item_data);
                $(document).on("change", "#unit", section_select);
                $(document).on("keyup", ".qty_needs, .unit_rate", qty_calculate);
                $(document).on("click", ".unit_rate", function(){
                    if (this.value === '0') {
                    this.value = '';
                }
                });
            });
        })(jQuery);
        
        function add_new_item() {

            if ($("tbody input").val() == null || !$("tbody input").val()) {
                alert("please insert!");
                return false;
            }

            let dynamic_id = new Date().getTime();

            let id = $(document).find("#purchase_table>tbody>tr").length + 1;

            let tr = `
                <tr id="general_store_${id}">
                    <td>
                        <select id="product_title_${dynamic_id}" class="form-control product_title " required></select>
                    </td>
                    <td>
                        <input type="text" name="description" class="form-control description" id="description" value="{{ $item->description }}" >
                    </td>

                    <td>
                        <input type="text"  id="qty_needs" name="qty_needs"
                            class="form-control qty_needs" required>
                    </td>

                    <td>
                        <input type="text" name="purpose" class="form-control purpose" id="purpose">
                    </td>
                    
                    <td>
                        <input type="text" name="unit_rate" class="form-control unit_rate">
                    </td> 
                    <td>
                        <input type="number" class="form-control total_amount" name="total_amount[]" id="total-amount"  readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control remarks" name="remarks" id="remarks">
                    </td>
                    <td class="text-center">
                        <a class="remove_item" href="javascript:void(0);"><i
                                class="fa fa-times text-danger"></i></a>
                    </td>
                </tr>
                    `;
            $("#purchase_table tbody").append(tr);
            product_title(`#product_title_${dynamic_id}`);
        }
        function removed_items_row() {
            if (confirm("Are you sure?")) $(this).closest("tr").remove();
        }

        function qty_calculate() { 
            let el                = $(this).closest('tr'),
                qty               = el.find('.qty_needs').val(),
                unit_rate         = el.find('.unit_rate').val();
            let total_amount      = qty * unit_rate ;
            let formatTotalAmount = (total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            el.find('.total_amount').val((formatTotalAmount));

            let grandtotal = 0;
            $("#purchase_table .total_amount").each(function(){
                grandtotal+=+($(this).val().replace(/,/g,""))
            })
            let formatGrandtotal= (grandtotal).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            $("#grandtotal").text(formatGrandtotal)
        }

        function product_title(selector = null) {
            let product_id =  $('#product_title').attr('value');
            selector = selector != null ? selector : '.product_title';
            $.get('/purchase-requisitions/get-product-title', function (product_title) {
                $(selector).select2({
                    placeholder: 'SELECT',
                    data: product_title,
                }).val(product_id).trigger('change');
            });
        }
        async function product_select(selector = null) {
            const resp = await fetch("/purchase-requisitions/get-product-title");
            const data = await resp.json();
            for (const title of $(".product_title")) {
            const title_id = $(title).attr("value");
            
            $(title)
                .select2({
                placeholder: "SELECT",
                data,
                })
                .val(title_id)
                .trigger("change");
            }
        }
        function unit_select(selector = null) {
            selector = selector != null ? selector : '.unit';
            $.get('/purchase-requisitions/get-unit', function (section) {
                $(selector).select2({
                    placeholder: 'SELECT SECTION',
                    data: section,
                }).val($('#unit').attr('value')).trigger('change');
            });
        }
        function section_select(){
            let section_id =  $('#unit').val();
            $.get('/purchase-requisitions/get-section/' + section_id, function (unit) {
                $('.section').html('');
                $('.section').select2({
                    placeholder: 'SELECT UNIT',
                    data: unit,
                }).val($('#section').attr('value')).trigger('change');
            });
        }
        function update() {
            let json_data = {}; 
            json_data.id                   = $("#purchase_edi_id").val();

            let tr_data = [];
            $("#purchase_requisition_tbody tr").each(function(){
                let item_data = {};
                let totalAmount = $(this).find('.total_amount').val();
                item_data.item_id       = $(this).find('.item_id').val();
		item_data.qty_needs     = $(this).find('.qty_needs').val() ?? 0;
                item_data.unit_rate     = $(this).find('.unit_rate').val() ?? 0;
                item_data.total_amount = totalAmount ? Number(totalAmount.replace(/,/g, "")) : 0;

                tr_data.push(item_data);
            });

            json_data.items = tr_data;

            $.ajax({
                url    : "/purchase-requisitions/p-user-update",
                method : "post",
                data   : json_data,
                type   : "json",
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                },
                success: function (res) {

                    swalInit.fire({
                        title   : res.msg,
                        type    : 'success'
                    }).then(function(){
                        window.location = "/purchase-requisitions/pending-requisition/";
                    }); 
                    
                },
                error: function (err) {
                    console.log(err);
                    swalInit.fire({
                        title   : err.responseJSON.msg ?? "Something Went Wrong",
                        type    : 'error'
                    });
                },
            });
        }
        function get_purchases_item_data(e) {
            e.preventDefault();
            let id = $(this).closest('tr').find('.approve_btn').data('id');
            $.ajax({
                type: "get",
                url: "/purchase-requisitions/purchases-item-data/" + id,
                dataType: "json",
                success: function (response) {
                    $('#modal-tbody').html("");
                    $.each(response.purchases_item, function(key, item) { 
                        
                        let tr = `
                                <tr>
                                    <td>
                                    <input type="hidden" class="form-control purchases_item_id" data-id="${item.id}" id="purchases_item_id">
                                        ${ ++key}
                                    </td>
                                    <td>
                                        ${item.product_title}
                                    </td>
                                    <td>
                                        ${item.qty}
                                    </td>
                                    <td>
                                        ${item.qty_approves}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control approve_qty" id="approve_qty" name="approve_qty" placeholder="TYPE Approve Qty" />
                                    </td>
                                    
                                </tr>`
                        $('#modal-tbody').append(tr);
                    });                
                }
            });
            
        }
     </script>
    @endpush
</x-pondit-limitless-master>
