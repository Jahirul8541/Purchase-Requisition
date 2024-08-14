<x-pondit-limitless-master>
    <x-pondit-limitless-toast js="0" />
    <x-pondit-pl-card cardTitle="CREATE PURCHASE REQUISITION">
            <div class="row">
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="unit"> <b> UNIT: </b></label>
                    <select id="unit" class="form-control unit" required ></select>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="section"> <b> SECTION: </b></label>
                    <select id="section" class="form-control section" required ></select>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person"> <b> CONTACT PERSON </b></label>
                    <input type="text" name="contact_person" class="form-control contact_person" id="contact-person" placeholder="Contact Person" >
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person-phone"> <b> CONTACT PERSON PHONE</b></label>
                    <input type="number" name="contact_person_phone" id="contact-person-phone"
                        class="form-control contact_person_phone" placeholder="Enter Phone Number" required  >
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="order-by"> <b>ORDER BY</b></label>
                    <input type="text" name="order_by" id="order_by"  class="form-control order_by"
                        placeholder="Enter Order" required  >
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="requisition-by-name"> <b>REQUISITED BY</b></label>
                    <input type="text" name="requisition_by_name" id="requisition-by-name" class="form-control requisition_by_name" placeholder="Enter Requisition By Name" value="{{ auth()->user()->name }}">
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="designation"> <b>DESIGNATION</b></label>
                    <input type="text" name="designation" id="designation" class="form-control designation"
                        placeholder="Enter Designation" required  >
                </div>
            </div>
            <div class="table-responsive">
                <table id="purchase_table" class="table table-bordered table-striped overflow-x:auto ">
                    <thead>
                        <tr class="bg-success text-white" align="center">
                            <th style="width:200px">ITEM NAME</th>
                            <th>REQUEST QTY</th>
                            <th>UOM TITLE</th>
                            <th>PURPOSE</th>
                            {{-- <th>Total Amount</th> --}}
                            <th>ITEM DESCRIPTION</th>
                            <th>REMARKS </th>
                            <th>ACTION</th>
                        </tr>
                    </thead>

                    <tbody id="purchase_requisition_tbody">
                            <tr>
                                <td>
                                    <select id="product_title" name="product_title" class="form-control product_title " required></select>
                                </td>
                                <td>
                                    <input type="number" name="qty_needs" class="form-control qty_needs" required>
                                </td>
                                <td>
                                    <input type="text" name="" class="form-control text-center uomTitle" readonly>
                                </td>
                                <td>
                                    <input type="text" name="Purpose" class="form-control purpose">
                                </td>
                                {{-- <td>
                                    <input type="number" class="form-control total_amount" name="total_amount" readonly >
                                </td> --}}
                                <td>
                                    <input type="text" name="description" class="form-control description" id="description" placeholder="description" >
                                </td>
                                <td>
                                    <input type="text" class="form-control remarks text-danger" name="remarks">
                                </td>
                                <td class="text-center">
                                    <a class="remove_item" href="javascript:void(0);"><i
                                            class="fa fa-times text-danger"></i></a>
                                </td>
                            </tr>
                        
                    </tbody>
                </table>
            </div>
            <div class="pt-2 row" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <button type="button" class="add_item btn float-left btn-sm mr-5 bg-success mb-2">
                        <i class="fa fa-plus"></i>
                        ADD NEW ITEM
                    </button>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <button class="float-right btn bg-success save_data" type="submit">{{ __('Submit') }}</button>
                </div>
            </div>
        {{-- </form> --}}
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
        <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition.js') }}"></script>
        <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/numberToWordConvert.js') }}"></script>

    @endpush
</x-pondit-limitless-master>
