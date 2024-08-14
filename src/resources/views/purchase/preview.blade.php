<x-pondit-limitless-master>
    <section>
       <x-pondit-pl-card cardTitle="PURCHASE REQUISITION PREVIEW">
       @php
           $query = new Btal\PurchaseRequisition\Models\PurchaseRequisitions();
           $units = DB::table('units')
                   ->where('id', $purchaseRequisitions->unit_id)
                   ->first();
       @endphp
       <table width="100%">
           <tr>
               <td class="text-center">
                   <h2 class="mb-0">MASCOM COMPOSITE LTD</h2>
                   <p class="mb-0"> {{$units->address}}</p>
                   <h3>PURCHASE REQUISITION</h3>
               </td>
           </tr>
       </table>
       <table width="100%" cellspacing="10">
               <tr>
                   <td width="55%">
                       <table width="100%">
                           <tr>
                               <td width="40%">UNIT NAME: </td>
                               <td>{{ $purchaseRequisitions->unit_title }}</td>
                           </tr>
                           <tr>
                               <td>SECTION:</td>
                               <td> {{ $purchaseRequisitions->section_title }}</td>
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
           <table class="table table-bordered table-responsive main text-center " width="100%" id="PurchasePreviewTable" >
               <tr class="bg-secondary" >
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
                   <td colspan="5">12</td>
                @if ($purchaseRequisitions->po_budget_complete == "true")
                   <td>13</td>
                @endif
               </tr>
   
               <tr>
                   <th rowspan="2">SL NO</th>
                   <th rowspan="2">ITEM NAME</th>
                   <th rowspan="2">ITEM DESCRIPTION</th>
                   <th rowspan="2">QTY NEEDS</th>
                   <th rowspan="2">STOCK QTY</th>
                   <th rowspan="2">QTY APPROVED</th>
                   <th rowspan="2">UNIT RATE</th>
                   <th rowspan="2">TOTAL TAKA</th>
                   <th rowspan="2">USE LOCATION / PURPOSE</th>
                   <th rowspan="2">REMARKS</th>
                   <th rowspan="2">AUTHORIZED COMMENT</th>
                   <th colspan="5">LAST PURCILASE INFORMATION</th>
                @if ($purchaseRequisitions->po_budget_complete == "true")
                   <th rowspan="2">PO/BUDGET NO</th>
                @endif
               </tr>
               <tr>
                   <td>DATE</td>
                   <td>REQ NO</td>
                   <td>UNIT RATE</td>
		           <td>QTY</td>
                   <td>TAKA</td>
               </tr>
               <tbody>
                       @foreach ($purchaseRequisitions->items as $key => $item) 
                           @php
                               $product = DB::table('products')->where('id', $item->product_id)->first();
                               $total_rate = (($item->qty_approves) * ($item->unit_rate));
                               $last_purchase=DB::table('purchase_requisitions')
                                                   ->join('purchase_requisition_items', 'purchase_requisition_items.purchase_requisitions_id', 'purchase_requisitions.id')
                                                   ->join('purchase_challan_items', 'purchase_challan_items.pur_req_item_id', 'purchase_requisition_items.id')
                                                   ->where('purchase_requisition_items.product_id', $item->product_id)
                                                   ->whereNotNull('purchase_requisition_items.challan_date')
                                                   ->selectRaw('
                                                            purchase_requisition_items.challan_date as date, 
                                                            purchase_challan_items.total as total_taka, 
                                                            purchase_challan_items.unit_rate,
							    purchase_challan_items.challan_qty, 
                                                            purchase_requisitions.purchase_requisition_no
                                                   ')
                                                   ->orderBy('purchase_requisition_items.id', 'DESC')
                                                   ->first();
		        @endphp
                               <tr>
                               <td>{{ $loop->iteration }}</td>
                               <td align="left">{{ $product->title }}</td>
                               <td align="left">{{ $item->description }}</td>
                               <td>{{ $item->qty}} - <span>{{ $product->uom_title }}</span></td>
                               <td>{{ $product->stock_qty}} - <span>{{ $product->uom_title }}</span></td>
                               <td>{{ $item->qty_approves }} - <span>{{ $product->uom_title }}</span></td>
                               <td>{{ number_format($item->unit_rate, 2, '.', ',') }}</td>
                               @if($item->qty_approves == 0.0 && is_null($purchaseRequisitions->authorized_at))
                               <td class="totalRate" align="right">{{ number_format(ceil(($item->qty) * ($item->unit_rate)), 2) }}</td>
                               @else
                              <td class="totalRate" align="right">{{ number_format(ceil(($item->qty_approves) * ($item->unit_rate)), 2) }}</td>
                               @endif
                               <td align="left">{{ $item->purpose }}</td>
                               <td style="color: red" align="left">{{ $item->remarks }}</td>
                               <td>{{ $item->comment }}</td>
                               <td>{{ $last_purchase !== null ?  date('d-M-Y', strtotime($last_purchase->date)) : 'N/A' }}</td>
                               <td>{{ $last_purchase !== null ? $last_purchase->purchase_requisition_no : 'N/A'}}</td>
                               <td>{{ $last_purchase !== null ? number_format($last_purchase->unit_rate, 2, '.', ',') : 0.00 }}</td>
			                   <td>{{ $last_purchase !== null ? $last_purchase->challan_qty . '-' .($product->uom_title) : 'N/A' }}</td>
                               <td>{{ $last_purchase !== null ? number_format($last_purchase->total_taka, 2, '.', ',') : 0.00 }}</td>
                            @if ($purchaseRequisitions->po_budget_complete == "true")
                               <td>{{$item->work_order_no.",". $item->budget_no }}</td>
                            @endif
                           </tr>
                        @endforeach
                       <tr>
                           <td colspan="7" class="text-danger" style="padding: 5px" align="right"><strong>GRAND TOTAL=</strong></td>
                           {{-- <td></td>
                           <td></td>
                           <td></td>
                           <td> </td> --}}
                           
                           <td  align="right" class="text-danger" id="grandTotal"></td>
                           <td colspan="6"></td>
                       </tr>
               </tbody>
       </table>
   
       <table class="signature_table text-center mt-5" width="100%" >
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
               <th width="20%" ><p style="text-decoration: overline;">PREPARED BY NAME</p></th>
               <th width="20%" ><p style="text-decoration: overline">AGM/DGM/GM/ED </p></td>
               <th width="20%" ><p style="text-decoration: overline">PURCHASE </p></td>
               <th width="20%" ><p style="text-decoration: overline">ACCOUNTS </p></td>
               <th width="20%"><p style="text-decoration: overline">AUTHORISED</p></td>
           </tr>
           <tr>
               <td>
                   @if (!is_null($purchaseRequisitions->created_by))
                       <b>{{ $query->get_user($purchaseRequisitions->data->created_by)->name }}</b>  <br>
                       <span>{{$purchaseRequisitions->user_fw_at   }}</span>
                   @endif
               </td>
               <td>
                   @if (!is_null($purchaseRequisitions->agmDgmGm_verified_at))
                   <b>{{ $query->get_user($purchaseRequisitions->data->agm_dgm_gm_user)->name }}</b>  <br>
                       <span>{{$purchaseRequisitions->agmDgmGm_verified_at}}</span>
                   @endif
               </td>
               <td>
                   @if (!is_null($purchaseRequisitions->purchase_verified_at))
                   <b>{{ $query->get_user($purchaseRequisitions->data->purchase_user)->name }}</b>  <br>
                       <span>{{$purchaseRequisitions->purchase_verified_at}}</span>
                   @endif
               </td>
               <td>
                   @if (!is_null($purchaseRequisitions->accounts_verified_at))
                   <b>{{ $query->get_user($purchaseRequisitions->data->accounts_user)->name }}</b>  <br>
                       <span>{{$purchaseRequisitions->accounts_verified_at}}</span>
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
       </x-pondit-pl-card>
     </section>
         <section>
           @php
               $user_roll = DB::table('users')
                   ->join('system_roles', 'users.active_role_id', 'system_roles.id')
                   ->where('users.id', auth()->user()->id)->first();
           @endphp
   
           @if ($user_roll->alias == 'purchase' && $purchaseRequisitions->purchase_comments == null)
               <x-pondit-pl-card cardTitle="Purchase Comment">
                   <div class="row form-group">
                       <div class="col-md-8 col-sm-12">
                           <label for="purchase_comment">Purchase Comment</label>
                           <input type="text" class="form-control purchase_comments" name="purchase_comments" id="purchase_comments" placeholder="Please Write Your Comments">
                       </div>
                       <div class="text-right mt-4 col-md-4 col-sm-12">
                           <button class=" btn btn-success btn-sm purchase_comment_save">Save</button>
                       </div>
                   </div>
               </x-pondit-pl-card>
           @endif
   
       <x-pondit-pl-card cardTitle="Forward Users">
           <form action="{{ route('purchases.saveUser') }}" method="POST">
               @csrf
               <input type="hidden"  name="purchases_req_id" class="purchases_req_id" value="{{ $purchaseRequisitions->id }}">
               <div  class="row">
                   @php
                       $agm_gm_user_disable    = '';
                       $purchase_user_disable  = 'disabled';
                       $account_user_disable   = 'disabled';
                       $authorize_user_disable = 'disabled';
                       if (auth()->user()->id == !is_null($purchaseRequisitions->created_by) ){
                           $purchase_user_disable  = 'disabled';
                           $account_user_disable   = 'disabled';
                           $authorize_user_disable = 'disabled';
                       }
   
                       if(!is_null($purchaseRequisitions->agm_dgm_gm_user))
                       {
                           $agm_gm_user_disable   = 'disabled';
                           
                       }
                       if(!is_null($purchaseRequisitions->agmDgmGm_verified_at) && is_null($purchaseRequisitions->purchase_user))
                       {
                           $purchase_user_disable = '';
                           $agm_gm_user_disable   = 'disabled';
                           $account_user_disable   = 'disabled';
   
                       }
                       if(!is_null($purchaseRequisitions->purchase_verified_at) && is_null($purchaseRequisitions->accounts_user))
                       {
                           $account_user_disable  = '';
                           $agm_gm_user_disable   = 'disabled';
                           $purchase_user_disable = 'disabled';
                       }
                       if( !is_null($purchaseRequisitions->accounts_verified_at) && is_null($purchaseRequisitions->authorized))
                       {
                           $authorize_user_disable = '';
                           $agm_gm_user_disable   = 'disabled';
                       }
                   
                   @endphp
                   <div class="mb-3 col-12 col-md-3 col-sm-6">
                       <input type="hidden" name="agm_dgm_gm" id="has-agm-dgm-gm" value="{{ $purchaseRequisitions->agm_dgm_gm_user }}">
                       <label class="form-label" for="hr-admin"> <b>Agm/Dgm/GM: </b></label>
                       <select id="agm-dgm-gm" class="form-control agm_dgm_gm" name="agm_dgm_gm" {{ $agm_gm_user_disable }}
                           placeholder="Enter Agm Dgm Gm"  required></select>
                   </div>
                   <div class="mb-3 col-12 col-md-3 col-sm-6">
                       <input type="hidden" id="has_purchase_user" name="purchase_user" value="{{ $purchaseRequisitions->purchase_user }}">
                       <label class="form-label" for="purchase"> <b>Purchase: </b></label>
                       <select id="purchase" class="form-control purchase" name="purchase_user" {{ $purchase_user_disable }}
                           placeholder="Enter Purchase" required ></select>
                   </div>
                   <div class="mb-3 col-12 col-md-3 col-sm-6">
                       <input type="hidden" id="has_accounts_user" name="accounts_user" value="{{ $purchaseRequisitions->accounts_user }}">
                       <label class="form-label" for="accounts"> <b>Accounts: </b></label>
                       <select id="accounts" class="form-control accounts" name="accounts_user" {{ $account_user_disable }}
                           placeholder="Enter Accounts" required></select>
                   </div>
                   <div class="mb-3 col-12 col-md-3 col-sm-6">
                       <input type="hidden" id="has_authorized_user" name="authorized" value="{{ $purchaseRequisitions->authorized}}">
                       <label class="form-label" for="authorized"> <b>Authorize Person: </b></label>
                       <select id="authorized" class="form-control authorized" name="authorized" {{ $authorize_user_disable }}
                           placeholder="Enter Authorized" required ></select>
                   </div>
               </div>
               @php
                   $is_visable = true;
                   if(
                       $agm_gm_user_disable    === 'disabled' &&
                       $purchase_user_disable  === 'disabled' &&
                       $account_user_disable   === 'disabled' &&
                       $authorize_user_disable === 'disabled'
                   )
                   {
                       $is_visable = false;
                   }
                   if($purchaseRequisitions->agm_dgm_gm_user == auth()->user()->id && is_null($purchaseRequisitions->agmDgmGm_verified_at) || $purchaseRequisitions->agm_dgm_gm_user == auth()->user()->id && !is_null($purchaseRequisitions->purchase_user))
                   {
                       $is_visable = false;
                   }
                   if($purchaseRequisitions->purchase_user == auth()->user()->id && is_null($purchaseRequisitions->purchase_verified_at) || $purchaseRequisitions->purchase_user == auth()->user()->id && !is_null($purchaseRequisitions->accounts_user))
                   {
                       $is_visable = false;
                   }
                   if($purchaseRequisitions->accounts_user == auth()->user()->id && is_null($purchaseRequisitions->accounts_verified_at ))
                   {
                       $is_visable = false;
                   }
                  
               @endphp
   
               <div class="row">
                   <div class="col-6 col-sm-6 col-md-6 col-lg-6  mt-2">
                       @if ($user_roll->alias == 'authorized' && is_null($purchaseRequisitions->authorized_at ) &&($purchaseRequisitions->rejection == 0))
                           <a class="btn btn-danger btn-sm " id="" data-id="" data-toggle="modal" data-target="#reject_modal" style="cursor: pointer">
                               Reject
                           </a>
                       @endif
                   @php
                   $display_none = true;
                   if($purchaseRequisitions->agm_dgm_gm_user == auth()->user()->id && !is_null($purchaseRequisitions->agmDgmGm_verified_at))
                   {
                       $display_none = false;
                   }
                   if($purchaseRequisitions->purchase_user == auth()->user()->id && !is_null($purchaseRequisitions->purchase_verified_at))
                   {
                       $display_none = false;
                   }
                   if($purchaseRequisitions->accounts_user == auth()->user()->id && !is_null($purchaseRequisitions->accounts_verified_at ))
                   {
                       $display_none = false;
                   }
                   if($purchaseRequisitions->authorized == auth()->user()->id && !is_null($purchaseRequisitions->authorized_at ))
                   {
                       $display_none = false;
                   }
                   @endphp
                   @if ($user_roll->alias == 'DGM')
                       @if ($display_none)
                           <a class="btn btn-warning btn-sm " id="forward-user" data-id="" data-toggle="modal" data-target="#forward_back_modal" style="cursor: pointer">
                               Forward Back
                           </a>
                       @endif
                       @elseif ($user_roll->alias == 'purchase')
                       @if ($display_none)
                           <a class="btn btn-warning btn-sm " id="forward-user" data-id="" data-toggle="modal" data-target="#forward_back_modal" style="cursor: pointer">
                               Forward Back
                           </a>
                       @endif
                       @elseif ($user_roll->alias == 'Accounts')
                       @if ($display_none)
                           <a class="btn btn-warning btn-sm " id="forward-user" data-id="" data-toggle="modal" data-target="#forward_back_modal" style="cursor: pointer">
                               Forward Back
                           </a>
                       @endif
                       @elseif ($user_roll->alias == 'authorized')
                       @if ($display_none)
                           <a class="btn btn-warning btn-sm " id="forward-user" data-id="" data-toggle="modal" data-target="#forward_back_modal" style="cursor: pointer">
                               Forward Back
                           </a>
                       @endif
                   @endif
               </div>
   
                   @if($user_roll->alias == 'view')
                           {{-- Only autolize user can show forward botton--}}
                   @else
                       <div class="col-6 col-sm-6 col-md-6 col-lg-6 float-right mt-2">
                           @if ($purchaseRequisitions->created_by ==  auth()->user()->id)
                           @elseif($user_roll->alias == 'DGM'  && $purchaseRequisitions->agmDgmGm_verified_at != null && $purchaseRequisitions->agm_dgm_gm_fw_at ==null )
                               <a class="btn bg-success float-right approved_back">Approved back</a>
                           @elseif($user_roll->alias == 'Accounts' && $purchaseRequisitions->accounts_verified_at != null && $purchaseRequisitions->accounts_fw_at ==null )
                               <a class="btn bg-success float-right approved_back">Approved back</a>
                           @elseif($user_roll->alias == 'purchase' && $purchaseRequisitions->purchase_verified_at != null && $purchaseRequisitions->purchase_fw_at ==null)
                               <a class="btn bg-success float-right approved_back">Approved back</a>
                           @elseif($user_roll->alias == 'authorized' && $purchaseRequisitions->authorized_at != null)
                               <a class="btn bg-success float-right approved_back">Approved back</a>
                           @endif
   
                           @if ($is_visable )
                               <button class="btn bg-success float-right mr-2" type="submit">Forward</button>
                           @endif
                       </div>
                   @endif
               </div>
               @if (!is_null($purchaseRequisitions->fw_bac_comment) && $purchaseRequisitions->fw_to_user_id == auth()->user()->id)
                   <div class="row">
                       <div class="col-12 form-group">
                           <label for=""> <b style="text-decoration:underline">Back Comment :</b> </label> <br>
                           {{ $purchaseRequisitions->fw_bac_comment }}
                           <br>
                           <label for=""><b style="text-decoration: underline">Forward Back By</b></label> <br>
                            {{$query->get_user($purchaseRequisitions->fw_form_user_id)->name}}
                       </div>
                   </div>
               @endif
           </form>
       </x-pondit-pl-card>
         </section>
   
       <x-pondit-pl-card cardTitle="Approval Users">
           <table class="table table-responsive table-bordered text-center" width="100%">
               <thead>
                   <tr class="bg-success">
                       <th class="text-center">Authorize</th>
                       <th class="text-center">Authorize Approve</th>
                       <th class="text-center">AGM/DGM/GM</th>
                       <th class="text-center">AGM/DGM/GM Approved</th>
                       <th class="text-center">Purchase</th>
                       <th class="text-center">Purchase Approved</th>
                       <th class="text-center">Accounts</th>
                       <th class="text-center">Accounts Approved</th>
                   </tr>
               </thead>
               @php
               $due_qty = $purchaseRequisitions->purchase_items->sum('qty') - $purchaseRequisitions->purchase_items->sum('qty_approves');
               @endphp
               <tbody>
                   <tr>
                       @php
                       $role_id = DB::table('system_roles')
                           ->where('alias', 'authorized')
                           ->first()->id;
                   @endphp
                   @if (auth()->user()->id == $purchaseRequisitions->authorized )
                       <td class="text-center">
                           @if ($due_qty > 0)
                               <a class="btn {{ !is_null($purchaseRequisitions->authorized_at) ? 'bg-success' : 'bg-warning' }} btn-sm approve_btn" data-id="{{ $purchaseRequisitions->id }}" data-id="" data-toggle="modal"
                                   data-target="#item_modal" style="cursor: pointer">
                                   <i class="fa fa-pen"></i>
                                   Approve
                               </a>
                           @else
                               <span class="badge bg-success">Completed</span>
                               @if (!is_null($purchaseRequisitions->authorized_at))
                                   <p class="date">{{ date('d-M-Y', strtotime($purchaseRequisitions->authorized_at)) }}</p>
                               @endif
                           @endif
                       </td>
                   @else
                       <td>
                           <a style="cursor: pointer" class="btn bg-warning btn-sm"
                               onClick='alert("You Are Not Authorized For This Action!")'>
                               <i class="fa fa-pen"></i>
                               Approve</a>
                       </td>
                   @endif
                   
                   @if (!is_null($purchaseRequisitions->authorized_at))
                       <td>{{ $query->get_user($purchaseRequisitions->authorized)->name }}</td>
                   @elseif ($purchaseRequisitions->rejection == 1)
                       <td class="text-danger font-weight-bold">Rejected</td>
                   @else
                       <td class="text-warning font-weight-bold">Waiting</td>
                   @endif
   
                   @if (!is_null($purchaseRequisitions->agm_dgm_gm_user))
   
                       <td>{{ $query->get_user($purchaseRequisitions->agm_dgm_gm_user)->name}}</td>
                       @if (!is_null($purchaseRequisitions->agmDgmGm_verified_at))
                           <td>{{ date('d-M-Y', strtotime($purchaseRequisitions->agmDgmGm_verified_at)) }}</td>
                       @elseif (auth()->user()->id == $purchaseRequisitions->agm_dgm_gm_user ||
                               auth()->user()->isSuperAdmin())
                           <td>
                               <a class="btn btn-sm bg-success user" data-value="agm_dgm_gm_user"
                                   data-id="{{ $purchaseRequisitions->id }}" style="cursor: pointer">
                                   Approve
                               </a>
                           </td>
                       @else
                           <td class="text-warning font-weight-bold">Waiting</td>
                       @endif
                   @else
                       <td class="text-info font-weight-bold">Not Assign</td>
                       <td class="text-info font-weight-bold">Not Assign</td>
                   @endif
   
                   @if (!is_null($purchaseRequisitions->purchase_user))
                       <td>{{ $query->get_user($purchaseRequisitions->purchase_user)->name }}</td>
                       @if (!is_null($purchaseRequisitions->purchase_verified_at))
                           <td>{{ date('d-M-Y', strtotime($purchaseRequisitions->purchase_verified_at)) }}</td>
                       @elseif (auth()->user()->id == $purchaseRequisitions->purchase_user ||
                               auth()->user()->isSuperAdmin())
                           <td>
                               <a class="btn btn-sm bg-success user" data-value="purchase_user"
                                   data-id="{{ $purchaseRequisitions->id }}" style="cursor: pointer">
                                   Approve
                               </a>
                           </td>
                       @else
                           <td class="text-warning font-weight-bold">Waiting</td>
                       @endif
                   @else
                       <td class="text-info font-weight-bold">Not Assign</td>
                       <td class="text-info font-weight-bold">Not Assign</td>
                   @endif
   
                   @if (!is_null($purchaseRequisitions->accounts_user))
                       <td>{{ $query->get_user($purchaseRequisitions->accounts_user)->name }}</td>
                       @if (!is_null($purchaseRequisitions->accounts_verified_at))
                           <td>{{ date('d-M-Y', strtotime($purchaseRequisitions->accounts_verified_at)) }}</td>
                       @elseif (auth()->user()->id == $purchaseRequisitions->accounts_user ||
                               auth()->user()->isSuperAdmin())
                           <td>
                               <a class="btn btn-sm bg-success user" data-value="accounts_user"
                                   data-id="{{ $purchaseRequisitions->id }}" style="cursor: pointer">
                                   Approve
                               </a>
                           </td>
                       @else
                           <td class="text-warning font-weight-bold">Waiting</td>
                       @endif
                   @else
                       <td class="text-info font-weight-bold">Not Assign</td>
                       <td class="text-info font-weight-bold">Not Assign</td>
                   @endif
                   
               </tr>
               </tbody>
           </table>
           <x-slot name="cardFooter">
               <div></div>
               <div>
                   <x-pondit-pl-btn-create icon="list" tooltip="List" href="{{ route('pending.requisition') }}">
                       {{ __('List') }}</x-pondit-pl-btn-create>
               </div>
               <div></div>
           </x-slot>
       </x-pondit-pl-card>
   
   
           {{-- Reject Modal start--}}
           <div  class="modal fade w3-button w3-black" id="reject_modal" data-backdrop="static" data-keyboard="false"
               ="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 9999999">
               <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                   <div class="modal-content">
                       <div class="modal-header bg-success text-center">
                           <h3 class="modal-title w-100" id="staticBackdropLabel"> Reject Comment</h3>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                       </div>
                       <div class="modal-body" id="reject">
                           <table id="module_table" class="table table-bordered table-striped overflow-x:auto ">
                               <thead>
                                   <tr class="bg-success text-white">
                                       <th><b>Reject Commetn</b></th>
                                   </tr>
                               </thead>
                               <tbody>
                                   <tr>
                                       <td>
                                           <input type="text" name="reject_comment" id="reject_comment" class="form-control reject_comment"  placeholder="REJECT COMMENT">
                                           {{-- <input type="hidden" name="id" id="id" class="form-control id" value="{{ $purchase->id }}"> --}}
                                       </td>
                                   </tr>
                               </tbody>
                               <tfoot></tfoot>
                           </table>
   
                           <div class="pt-2" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                               <button type="submit" id="reject_btn"
                                   class="float-right btn btn-sm bg-danger mr-2 mb-2 reject_comment_save">
                                   Reject
                               </button>
                           </div>
                       </div>
                       <div class="pt-2" style="border-top:1px solid #a5a9acb8">
                           <button class="btn btn-secondary btn-sm float-right mr-2 mb-2"
                               data-dismiss="modal">Close</button>
                       </div>
                   </div>
               </div>
           </div>
           {{-- Reject Modal end--}}
   
           {{-- forward Back Modal start--}}
           <div  class="modal fade w3-button w3-black" id="forward_back_modal" data-backdrop="static" data-keyboard="false"
                   tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" >
               <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                   <div class="modal-content">
                       <div class="modal-header bg-success text-center">
                           <h3 class="modal-title w-100" id="staticBackdropLabel"> Forward Back</h3>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                       </div>
                       <div class="modal-body" id="forward_back">
                           <table id="module_table" class="table table-bordered table-striped overflow-x:auto ">
                               <thead>
                                   <tr class="bg-success text-white">
                                       <th><b>Forward Back User</b></th>
                                       <th><b>Forward Back Comment</b></th>
                                   </tr>
                               </thead>
                               <tbody>
                                   <tr>
                                       <td>
                                           <select name="forward_back_user" id="forward_back_user" class="form-control forward_back_user"></select>
                                       </td>
                                       <td>
                                           <input type="text" name="fw_bac_comment" id="fw_bac_comment" class="form-control fw_bac_comment"  placeholder="FORWARD BACK COMMENT">
                                       </td>
                                   </tr>
                               </tbody>
                               <tfoot></tfoot>
                           </table>
   
                           <div class="pt-2" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                               <button type="submit" id="forward_back_btn"
                                   class="float-right btn btn-sm bg-success mr-2 mb-2 forward_back"><i class="fa fa-save"></i>
                                   SAVE
                               </button>
                           </div>
                       </div>
                       <div class="pt-2" style="border-top:1px solid #a5a9acb8">
                           <button class="btn btn-secondary btn-sm float-right mr-2 mb-2"
                               data-dismiss="modal">Close</button>
                       </div>
                   </div>
               </div>
           </div>
           {{-- forward Back Modal end--}}
           
           {{-- modal --}}
           <div style=""  class="modal fade w3-button w3-black" id="item_modal" data-backdrop="static" data-keyboard="false"
               tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 9999999">
               <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                   <div class="modal-content approve_modal">
                       <div class="modal-header bg-success text-center">
                           <h3 class="modal-title w-100" id="staticBackdropLabel"> APPROVE RQUISIOTION</h3>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                       </div>
                       <div class="modal-body" id="item_modal">
                           <table id="module_table" class="table table-responsive table-bordered table-striped overflow-x:auto ">
                               <thead>
                                   <tr class="bg-success text-white">
                                       <th>SL</th>
                                       <th>Purchase Item</th>
                                       <th>Total Qty</th>
                                       <th>Pre Approved Qty</th>
                                       <th>Approve Qty</th>
                                       <th>Comment</th>
                                   </tr>
                               </thead>
                               <tbody id="modal-tbody">
                                   
                               </tbody>
                               
                               <tfoot></tfoot>
                           </table>
   
   
                       <div class="row pt-2" style="border-top:1px solid #a5a9acb8;margin-top: 5px;">
                           
                           <div class="col-12 text-right">
                               <button type="submit" id="modal-save" class=" btn btn-sm bg-success mr-2 mb-2 modal_save"><i class="fa fa-save"></i>
                                   Approve
                               </button>
   
                               {{-- <button type="submit" id="ApproveBtn" class="d-none btn btn-sm bg-success mr-2 mb-2"><i class="fa fa-save"></i>
                                   Approve
                               </button> --}}
                           </div>
                       </div>
                       <div class="pt-2" style="border-top:1px solid #a5a9acb8">
                           <button type="button" class="btn btn-secondary btn-sm float-right mr-2 mb-2"
                               data-dismiss="modal">Close</button>
                       </div>
                   </div>
               </div>
           </div>
           @push('css')
               <style>
                   @media only screen and (min-width: 500px){
                       .approve_modal{
                           width:50rem !important;
                       }
                   }
   
   
                   input{
                       padding: 0px !important
                   }
                   #PurchasePreviewTable td, #PurchasePreviewTable th{
                       padding: 0px 10px 0px 10px;
                   }
           </style>
           @endpush
   
           @push('js')
           <x-pondit-helper />
               <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/forward.js') }}"></script>
               <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/purchase-requisition.js') }}"></script>
               <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/numberToWordConvert.js') }}"></script>
   
           @endpush
   </x-pondit-limitless-master>
   
   
