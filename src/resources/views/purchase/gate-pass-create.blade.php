<x-pondit-limitless-master>

    <x-pondit-pl-card cardTitle="Create Gate Pass">
            <div class="row">
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="gate_pass_no"> <b> Gate Pass NO: </b></label>
                    <input type="hidden" name="purchase_requisition_id" class="purchase_requisition_id" value="{{ $purchaseRequisitions->id }}">
                    <input type="text" name="gate_pass_no" class="form-control gate_pass_no" id="gate_pass_no" placeholder="Enter gate Pass NO" >
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="sl_no"> <b> Sl No: </b></label>
                    <input type="text" name="sl_no" class="form-control sl_no" id="sl_no" placeholder="Sl NO" >
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="destination"> <b> Destination: </b></label>
                    <input type="text" name="destination" class="form-control destination" id="destination" placeholder="Enter Destination" >
                </div>
            </div>
            <div class="table-responsive">
                <table id="gate_pass" class="table table-bordered table-striped overflow-x:auto ">
                    <thead>
                        <tr class="bg-success text-white" align="center">
                            <th>Product Title</th>
                            <th>QTY</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach ($purchases_requisition_items as $item)
                            <tr id="gate_pass">
                                <td>

                                    <input type="hidden" name="product_id" class="product_id" value="{{ $item->product_id }}" readonly>
                                    <input class="form-control" value="{{ $item->product_title }}" readonly >
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control qty" value="{{ $item->qty }}" readonly required>
                                </td>
                                <td>
                                    <input type="text" class="form-control remarks" id="remarks">
                                </td>
                                <td class="text-center">
                                    <a class="remove_item" href="javascript:void(0);"><i
                                            class="fa fa-times text-danger"></i></a>
                                </td>
                            </tr>
                        @endforeach
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
        <x-slot name="cardFooter">
            <div></div>
            <div>
                <x-pondit-pl-btn-create icon="list" tooltip="List" href="{{ route('gate.index') }}">
                    {{ __('List') }}</x-pondit-pl-btn-create>
            </div>
            <div></div>
        </x-slot>
    </x-pondit-pl-card>


    @push('css')
    @endpush
    @push('js')
        <x-pondit-helper />
        <script src="{{ asset('vendor/BTAL/requisitionmanagement/assets/js/gate.js') }}"></script>
    @endpush
</x-pondit-limitless-master>
