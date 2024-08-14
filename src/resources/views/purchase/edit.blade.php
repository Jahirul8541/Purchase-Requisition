<x-pondit-limitless-master>
    <x-pondit-limitless-toast />
    <x-pondit-pl-card cardTitle="PURCHASE REQUISITION EDIT">
           @php
               $user_roll = DB::table('users')
                   ->join('system_roles', 'users.active_role_id', 'system_roles.id')
                   ->where('users.id', auth()->user()->id)->first();
           @endphp
            <div class="row">
                <input type="hidden" name="purchase_edi_id" id="purchase_edi_id" class="purchase_edi_id" value="{{ $purchase_edit->id }}">
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="unit"> <b> UNIT : </b></label>
                    <select id="unit" class="form-control unit"
                        placeholder="Enter Unit" required  value="{{ $purchase_edit->unit_id }}"></select>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="section"> <b> SECTION : </b></label>
                    <select id="section" class="form-control section"
                        placeholder="Enter Section" required  value="{{ $purchase_edit->section_id }}"></select>
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person"> <b> CONTACT PERSON : </b></label>
                    <input type="text" name="contact_person" class="form-control contact_person" id="contact-person"
                        placeholder="Section" value="{{     $purchase_edit->contact_person    }}">
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="contact-person-phone"> <b> CONTACT PERSON PHONE: </b></label>
                    <input type="number" name="contact_person_phone" id="contact-person-phone"
                        class="form-control contact_person_phone" placeholder="Enter Phone Number" required
                        value="{{     $purchase_edit->contact_person_phone    }}">
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="order-by"> <b>ORDER BY : </b></label>
                    <input type="text" name="order_by" id="order_by" class="form-control order_by"
                        placeholder="Enter Order" required
                        value="{{     $purchase_edit->order_by    }}">
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="requisition-by-name"> <b> REQUISITED BY : </b></label>
                    <input type="text" name="requisition_by_name" id="requisition-by-name"
                        class="form-control requisition_by_name" placeholder="Enter Requisition-By-Name" required
                        value="{{     $purchase_edit->requisition_by_name    }}">
                </div>
                <div class="mb-3 col-12 col-md-3 col-sm-6">
                    <label class="form-label" for="designation"> <b> DESIGNATION : </b></label>
                    <input type="text" name="designation" id="designation" class="form-control designation"
                        placeholder="Enter Designation" required
                        value="{{     $purchase_edit->designation    }}">
                </div>
            </div>
            <div class="table-responsive">
                <table id="purchase_table" class="table table-bordered table-striped overflow-x:auto ">
                    <thead>
                        <tr class="bg-success text-white" align="center">
                            <th>ITEM NAME</th>
                            <th>REQUEST QTY</th>
                            <th>UOM TITLE</th>
                            @if($user_roll->alias == "it" || auth()->user()->isSuperAdmin()|| $user_roll->alias == "authorized")
                            <th>APPROVE QTY</th>
                            @endif
                            <th>PURPOSE </th>
                            <th>ITEM DESCRIPTION</th>
                            @if (!is_null($purchase_edit->purchase_user))
                            <th>UNIT RATE</th>
                            <th>TOTAL AMOUNT</th>
                            @endif
                            <th>REMARKS</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    
                    <tbody id="purchase_requisition_tbody">
                        @foreach ($purchase_edit->purchase_items as $item)
                            
                            <tr>
                                <input type="hidden" name="item_id[]" class="item_id" value="{{ $item->id }}">
                                <td>
                                    <select name="" id="" class="form-control product_title" value="{{ $item->product_id }}">
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="qty_needs[]" id="qty_needs"
                                        class="form-control qty_needs"
                                        value="{{   $item->qty    }}" required >
                                </td>
                                <td>
                                    <input type="text" name="" class="form-control text-center uomTitle" readonly>
                                </td>
                                @if($user_roll->alias == "it" || auth()->user()->isSuperAdmin()|| $user_roll->alias == "authorized")
                                    <td> 
                                        <input type="text" class="form-control qty_approves" name="qty_approves" id="qty_approves" value="{{$item->qty_approves}}">
                                    </td>
                                @endif
                                <td>
                                    <input type="text" class="form-control purpose" name="purpose[]"
                                        id="purpose" value="{{   $item->purpose  }}">
                                </td>
                                <td>
                                    <input type="text" name="description" class="form-control description" id="description" value="{{ $item->description }}" >
                                </td>
                                
                               
                                @if (!is_null($purchase_edit->purchase_user))
                                <td>
                                    <input type="text" class="form-control unit_rate" id="unit-rate"
                                        name="unit_rate[]" value="{{   $item->unit_rate    }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control total_amount" name="total_amount[]"
                                        id="total-amount" value="{{ $item->total_amount }}" readonly>
                                </td>
                                @endif
                                <td>
                                    <input type="text" class="form-control remarks text-danger" name="remarks[]"
                                        id="remarks" value="{{   $item->remarks    }}">
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
                    <button type="button" class="add_item  btn float-left btn-sm mr-5 bg-success mb-2">
                        <i class="fa fa-plus"></i>
                        ADD NEW ITEM
                    </button>
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
       
        <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition-edit.js') }}"></script>
    @endpush
</x-pondit-limitless-master>
