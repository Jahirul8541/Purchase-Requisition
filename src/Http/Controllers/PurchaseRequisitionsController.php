<?php

namespace Btal\PurchaseRequisition\Http\Controllers;

use DateTime;
use Exception;
use App\Models\User;
use Nette\Utils\Strings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use WeStacks\TeleBot\TeleBot;
use Pondit\Authorize\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Btal\Productmanagement\Models\ProductsManagement;
use Btal\PurchaseRequisition\Models\PurchaseRequisitions;
use Btal\PurchaseRequisition\Models\PurchaseRequisitionItems;
use BA\Merchandising\MerchandisingMasterData\Models\Signature;

class PurchaseRequisitionsController extends Controller
{
    public static $visiblePermissions = [
        'index'                         => 'Index',
        'create'                        => 'Create',
        'store'                         => 'Store',
        'item_delete'                   => 'Update Item Removed',
        'edit'                          => 'Edit',
        'p_user_edit'                   => 'Purchase User Edit',
        'update'                        => 'Update',
        'p_user_update'                 => 'Purchase User Update',
        'delete'                        => 'Delete',
        'show'                          => 'Show',
        'pending_requisition'           => 'Pending Requisition',
        'get_descriotion_of_good'       => 'Gate Description Of Good',
        'get_purchase_requisition_no'   => 'Get Purchases Req No',
        'purchases_item_data'           => 'Purchases Requisition Item Data',
        'purchases_approve_qty'         => 'Purchases Approved Qty',
        'purchases_item_qty'            => 'Purchases Item Qty',
        'purchases_updated_qty'         => 'Purchases Update Qty',
        'purchases_requisition_pdf'     => 'Purchases Requisition Pdf',
        'purchase_req_for_gate_pass'    => 'Purchases Requisition For Gate Pass',
        'get_requisition'               => 'Get Requisition',
        'user_wise_requisition_list'    => 'User Wise Requisition List',
        'purchases_requisition_preview' => 'Requisition Preview List',
        'store_forwarded_data'          => 'Store Forward',
        'purchase_comment'              => 'Purchase Comment',
        'approved_requisition'          => 'Approved Requisition',
        'user_wise_approved_report'     => 'User Wise Approved Report',
        'get_forward_view'              => 'Forward view',
        'get_store_user'                => 'Store user Api',
        'get_purchase_user'             => 'Purchase user Api',
        'get_agm_dgm_gm_user'           => 'Dgm gm Api',
        'get_accounts_user'             => 'Account Api',
        'get_authorize_user'            => 'Authorize Api',
        'get_product_title'             => 'Product Title Api',
        'get_product_data'              => 'Product Uom',
        'get_unit_select'               => 'Get Unit Select',
        'get_section_select'            => 'Get Section Select',
        'userApprove'                   => 'User Approved Update',
        'forwardApproved'               => 'Forward Approved Update',
        'forward_back_user'             => 'Forward Back User',
        'forward_back'                  => 'Forward Back Update',
        'requisition_rejection'         => 'Requisition Rejection',
        'requisition_rejection_list'    => 'Rejected Requisition List',
        'requisition_rejection_restore' => 'Rejected Requisition Restore',
        'approved_back'                 => 'Approved Back',
        'summary'                       => 'Summary Dashboard',
        'po_budget_complete_list'       => 'PO and Budget Complete List',
        'po_budget_preview'             => 'PO and Budget Preview',
        'all_requisition'               => 'All Requisitions List',
        'po_budget_pdf'                 => 'Po Budget Wise Pdf',
        
    ];


    // private $bot;
    // private $chat_id = 5524292189;
    // private $chat_id = -920458462;
    // public function __construct()
    // {
    //     $this->bot = new TeleBot('6640468548:AAFZUMOehd7PV0somkIxzC5j1s5tDxI_3BU');
    // }

    private $purchase_requisition_report = [
        [
            "report_name"   => "Purchase Requisition Report",
            "slug"          => "purchase-requisition-api",
            "request"       => "input-user",
            "request_value" => "",
        ]
    ];
    private function telegram_sms($purchase_requisition_id){
        $purchase_requisition = PurchaseRequisitions::where('id', $purchase_requisition_id)->first();
        $userInfo = DB::table('users')->where('id', $purchase_requisition->created_by)->first();
            $url = 'http://mascom.ba.com/purchase-requisitions/purchases-requisition-preview/'.$purchase_requisition->id;
            $displayText = 'Click here to View This Requisition';
            $hiddenLink = sprintf('<a href="%s" target="_blank">%s</a>', $url, $displayText);
            $message = "Mascom Composite LTD.\n------------------- New Requisition-------------------.\n";
            $message .= "Requisition NO : {$purchase_requisition->purchase_requisition_no}.\n";
            $message .= "Unit : {$purchase_requisition->unit_title}.\n";
            $message .= "Created By : {$userInfo->name}.\n";
            $message1 = "Barcodetech Automation Ltd.";
            $hiddenMessage = $message . $hiddenLink. "\n\n". $message1;

            $this->bot->sendMessage([
                'chat_id' => $this->chat_id,
                'text'    => $hiddenMessage,
                'parse_mode' => 'HTML',
            ]);
    }

    private function user_auth($user_id){
        $user_roll = DB::table('users')
                        ->join('system_roles', 'users.active_role_id', 'system_roles.id')
                        ->where('users.id', $user_id)->first();
        return $user_roll->alias;
    }

 public function data_sync(){

        $challans = PurchaseRequisitionItems::select('id', 'challan_date')
        ->get()
        ->map(function ($challan) {
            return [
                'id' => $challan->id,
                'formatted_date' => $challan->challan_date,
            ];
        })
        ->filter(function ($challan) {
            return preg_match("/^\d{1,2}\s(?:January|February|March|April|May|June|July|August|September|October|November|December),\s\d{4}$/", $challan['formatted_date']);
        });
 //       dd($challans);

        foreach ($challans as $key => $value) {
            $data = [
                'challan_date' => Carbon::createFromFormat('d F, Y', $value['formatted_date'])->format('d-M-Y')
            ];
            PurchaseRequisitionItems::where('id', $value['id'])->update($data);
        }
        return ('success');
    }

    public function index()
    {
        $user_id = auth()->user()->id;
        $roles = $this->user_auth( $user_id );
        if ($roles == 'it') {
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }
        elseif ($roles == 'super-admin') 
        {
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
                // dd($purchase_requisitions);                        
        }elseif($roles == 'guest'){
            
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.created_by', auth()->user()->id)
                                        ->where('purchase_requisitions.agm_dgm_gm_user', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'DGM'){

            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.agm_dgm_gm_user', auth()->user()->id)
                                        ->where('purchase_requisitions.agmDgmGm_verified_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'purchase'){

            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.purchase_user', auth()->user()->id)
                                        ->where('purchase_requisitions.purchase_verified_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'Accounts'){

            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.accounts_user', auth()->user()->id)
                                        ->where('purchase_requisitions.accounts_verified_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();

        }elseif($roles == 'authorized'){

            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized', auth()->user()->id)
                                        ->where('purchase_requisitions.authorized_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'view'){
            
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '==', 0)
                                        ->where('purchase_requisition_items.is_work_order',0)
                                        ->where('purchase_requisition_items.is_budget', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }else{
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '!=', null)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->where(function ($query) {
                                            $query->where('purchase_requisition_items.is_work_order',0)
                                            ->Where('purchase_requisition_items.is_budget', 0);
                                        })
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();  
        }
        return view('purchaserequisition::purchase.index', compact('purchase_requisitions'));
    }

    public function po_budget_complete_list(){

        $purchase_requisitions = DB::table('purchase_requisitions')
                                ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                ->where(function ($query) {
                                    $query->where('purchase_requisition_items.is_work_order',1)
                                    ->orWhere('purchase_requisition_items.is_budget', 1);
                                })
                                ->selectRaw('
                                    purchase_requisitions.*,
                                    group_concat(purchase_requisition_items.product_title) as item_title,
                                    sum(purchase_requisition_items.total_amount) as total
                                ')
                                ->groupBy('purchase_requisitions.id') 
                                ->orderBy('purchase_requisitions.id','DESC')
                                ->get();
            return view('purchaserequisition::purchase.po-budget', compact('purchase_requisitions'));

    }

    public function approved_back($id){
        
        $user_id = auth()->user()->id;
        $roles = $this->user_auth($user_id);
        if($roles == 'DGM'){
            PurchaseRequisitions::where('id', $id)
                                    ->update(['agmDgmGm_verified_at' => null]);
        }elseif($roles == 'purchase'){
            PurchaseRequisitions::where('id', $id)
                                    ->update(['purchase_verified_at' => null]);
        }elseif($roles == 'Accounts'){
            PurchaseRequisitions::where('id', $id)
                                    ->update(['accounts_verified_at' => null]);
        }elseif($roles == 'authorized'){

            PurchaseRequisitions::where('id', $id)
                                    ->update(
                                        ['authorized_at' => null],
                                        ['grand_total' => null],
                                    );
	    $purchases_items = PurchaseRequisitionItems::where('purchase_requisitions_id', $id)->get();
            foreach ($purchases_items as $value) {
               $product            = ProductsManagement::find($value->product_id);
               $request_qty        = $product->requisition_qty< $value->qty_approves ?0 :$product->requisition_qty - $value->qty_approves;
               $update_request_qty = $request_qty + $value->qty;

               $product->update([
                'requisition_qty' => $update_request_qty
               ]);
            }
            PurchaseRequisitionItems::where('purchase_requisitions_id', $id)
                                    ->update(
                                        ['qty_approves' => 0.00]
                                    );
        }
        return response()->json([
            "msg"  => "Requisition Approved Back Successfully ",
        ], 200);
    }

    public function pending_requisition(){
        $user_id = auth()->user()->id;
        $roles = $this->user_auth($user_id);
        if ($roles == 'super-admin') 
        {
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '=', null)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'guest'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.created_by', auth()->user()->id)
                                        ->where('purchase_requisitions.agm_dgm_gm_user', '=', null)
                                        ->orWhere('purchase_requisitions.fw_to_user_id', '=', auth()->user()->id)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'DGM'){
            
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.agm_dgm_gm_user', auth()->user()->id)
                                        ->where('purchase_requisitions.agmDgmGm_verified_at', '=', null)
                                        ->orWhere('purchase_requisitions.fw_to_user_id', '=', auth()->user()->id)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();

        }elseif($roles == 'purchase'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.purchase_user', auth()->user()->id)
                                        ->where('purchase_requisitions.purchase_verified_at', '=', null)
                                        ->orWhere('purchase_requisitions.fw_to_user_id', '=', auth()->user()->id)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'Accounts'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.accounts_user', '=', auth()->user()->id)
                                        ->where('purchase_requisitions.accounts_verified_at', '=', null)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();


        }elseif($roles == 'authorized'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized', auth()->user()->id)
                                        ->where('purchase_requisitions.authorized_at', '=', null)
                                        // ->orWhere('purchase_requisitions.fw_to_user_id', '=', auth()->user()->id)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();

        }elseif($roles == 'view'){

            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '=', null)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }else{
            
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.authorized_at', '=', null)
                                        ->where('purchase_requisitions.rejection', '=', 0)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }
        return view('purchaserequisition::purchase.pending', compact('purchase_requisitions'));
    }

    public function create()
    {
        return view('purchaserequisition::purchase.create');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if(is_null($request->section_id))
            throw new Exception('Please Select The Section!');
            if(is_null($request->unit_id))
            throw new Exception('Please Select The Unit!');

            $validator = Validator::make($request->all(), [
                'contact_person'      => 'required',
                'order_by'            => 'required',
                'requisition_by_name' => 'required',
                'designation'         => 'required',
            ]);

            if ($validator->fails()) {
                $validations = $validator->errors()->messages();
                $errorsArray = "";
                foreach ($validations as $field_name => $errors) {
                    foreach ($errors as $errorMsg) {
                        $errorsArray = $errorsArray . $errorMsg . "<br>";
                    }
                }
                throw new Exception($errorsArray, 403);
            }

            $section = DB::table('sections')->where('id', $request->section_id)->first();
            $unit = DB::table('units')->where('id', $request->unit_id)->first();
            $purchase_requisition = PurchaseRequisitions::create([
                'uuid'                    => Str::uuid(),
                'floor_requisition_id'    => $request->floor_req_id ?? null,
                'purchase_requisition_no' => $this->get_purchase_requisition_no($section),
                'unit_id'                 => $request->unit_id,
                'section_id'              => $request->section_id,
                'unit_title'              => $unit->title,
                'section_title'           => $section->section_title,
                'contact_person'          => $request->contact_person,
                'contact_person_phone'    => $request->contact_person_phone,
                'order_by'                => $request->order_by,
                'requisition_by_name'     => $request->requisition_by_name,
                'designation'             => $request->designation,
                'created_by'              => Auth::user()->id,
            ]);

            $grand_total = 0;
            foreach ($request->items as $item) {
                $product = ProductsManagement::find($item['product_title']);
            
                $data = [
                    'uuid'                     => Str::uuid(),
                    'purchase_requisitions_id' => $purchase_requisition->id,
                    'product_id'               => $product->id,
                    'product_title'            => $product->title,
                    'description'              => $item['description'],
                    'qty'                      => $item['qty_needs'],
                    'unit_rate'                => $item['unit_rate'],
                    'actual_unit_rate'         => $item['unit_rate'],
                    'Purpose'                  => $item['purpose'],
                    'total_amount'             => $item['total_amount'],
                    'remarks'                  => $item['remarks'],
                ];
                PurchaseRequisitionItems::create($data);
                $grand_total += $item['total_amount'];
		$requisition_qty = $product->requisition_qty + $item['qty_needs'];
                $product->update([
                    'requisition_qty' => $requisition_qty,
                ]);
            }

            $purchase_requisition->update(['grand_total' => $grand_total]);
            
            $path = is_null($purchase_requisition->floor_requisition_id) ? '/purchase-requisitions/pending-requisition/' : '/floor-requisitions';

            // $this->telegram_sms($purchase_requisition->id);
            DB::commit();
          
            return response()->json([
                "msg"  => "Purchase Requisition Created Successfully",
                "req_no" => $purchase_requisition->purchase_requisition_no,
                "path" => $path
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                "msg"  => $e->getMessage(),
                "line" => $e->getLine()
            ], 400);
        }
    }

    public function show($id)
    {
        $purchase_show = PurchaseRequisitions::where('id', $id)->with('purchase_items')->first();
        foreach ($purchase_show->purchase_items as $key => $value) {
            $value->uom_title = ProductsManagement::where('id', $value->product_id)->first()->uom_title;
        }
        return view('purchaserequisition::purchase.show', compact('purchase_show'));
    }

    public function edit($id)
    {
        $products = ProductsManagement::select('id', 'title as text' )->get();
        $purchase_edit = PurchaseRequisitions::where('id', $id)->with('purchase_items')->first();
        return view('purchaserequisition::purchase.edit', compact('purchase_edit', 'products'));
    }

    public function p_user_edit($id)
    {
        $products = ProductsManagement::select('id', 'title as text' )->get();
        $purchase_edit = PurchaseRequisitions::where('id', $id)->with('purchase_items')->first();
        return view('purchaserequisition::purchase.p-user-edit', compact('purchase_edit', 'products'));
    }

    public function update(Request $request)
    {
        $user_id = auth()->user()->id;
        $roles = $this->user_auth( $user_id );
        try {
            DB::beginTransaction();
            $section = DB::table('sections')->where('id', $request->section_id)->first();
            $unit = DB::table('units')->where('id', $request->unit_id)->first();
            PurchaseRequisitions::where('id', $request->id)->update([
                'unit_id'                 => $request->unit_id,
                'section_id'              => $request->section_id,
                'unit_title'              => $unit->title,
                'section_title'           => $section->section_title,
                'contact_person'          => $request->contact_person,
                'contact_person_phone'    => $request->contact_person_phone,
                'order_by'                => $request->order_by,
                'requisition_by_name'     => $request->requisition_by_name,
                'designation'             => $request->designation,
                'updated_by'              => Auth::user()->id
            ]);
            $grand_total = 0;
            
            foreach ($request->items as $item) {
                $product = ProductsManagement::find($item['product_title']);
                $itemData = [
                    'uuid'                     => Str::uuid(),
                    'purchase_requisitions_id' => $request->id,
                    'product_id'               => $product->id,
                    'product_title'            => $product->title,
                    'description'              => $item['description'],
                    'qty'                      => $item['qty_needs'],
                    'qty_approves'             => $item['qty_approves'] ?? 0,
                    'unit_rate'                => $item['unit_rate'] ?? 0,
                    'actual_unit_rate'         => $item['unit_rate'] ?? 0,
                    'purpose'                  => $item['purpose'],
                    'total_amount'             => $item['total_amount'] ?? 0,
                    'remarks'                  => $item['remarks'],
                ];
                if (array_key_exists('item_id', $item)) {
                    $requisition_item       = PurchaseRequisitionItems::where('id', $item['item_id'])->first();
                    $adjust_requisition_qty = $product->requisition_qty < $requisition_item->qty?$product->requisition_qty - 0 :$product->requisition_qty - $requisition_item->qty;
                    $update_requisition_qty = $adjust_requisition_qty + $item['qty_needs'];

                    $product->update([
                        'requisition_qty' => $update_requisition_qty,
                    ]);
                    $requisition_item->update($itemData);

                } else {
                    $purchase = PurchaseRequisitions::where('id', $request->id)->first();
                    $itemData['purchase_requisitions_id'] = $purchase->id;
                    PurchaseRequisitionItems::create($itemData);
                    $update_requisition_qty = $product->requisition_qty + $item['qty_needs'];
                    $product->update([
                        'requisition_qty' => $update_requisition_qty,
                    ]);
                }
                if ($item['qty_approves'] != 0) {
                    $grand_total += $item['total_amount'];
                }
            }
            PurchaseRequisitions::where('id',  $request->id)->update(['grand_total' => $grand_total]);
            DB::commit();
            
        if ($roles == 'it' || $roles == 'super-admin'|| $roles == "authorized") {
            $path = '/purchase-requisitions/index/';
            return response()->json([
                "msg" => "Update Successfully",
                "path" => $path
            ], 200);
        }else{
            $path ='/purchase-requisitions/pending-requisition/' ;
            return response()->json([
                "msg" => "Update Successfully",
                "path" => $path
            ], 200);
        }
            
        } catch (\Exception $e) {
            return response()->json([
                "msg"  => $e->getMessage(),
                "line" => $e->getLine()
            ], 400);
        }
    }

    public function p_user_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $grand_total = 0;
            foreach ($request->items as $item) {
                $itemData = 
		[
		    'qty'                      => $item['qty_needs'] ?? 0,
                    'unit_rate'                => $item['unit_rate'] ?? 0,
                    'actual_unit_rate'         => $item['unit_rate'] ?? 0,
                    'total_amount'             => $item['total_amount'] ?? 0,
                ];
                PurchaseRequisitionItems::where('id', $item['item_id'])->update($itemData);
                $grand_total += $item['total_amount'];
            }
            PurchaseRequisitions::where('id',  $request->id)->update(['grand_total' => $grand_total]);

            DB::commit();

            return response()->json([
                "msg" =>  "Unit Rate Update Successfully"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "msg"  => $e->getMessage(),
                "line" => $e->getLine()
            ], 400);
        }
    }

    public function delete($id)
    {
         PurchaseRequisitions::find($id)->delete();
       $requisiton_items =  PurchaseRequisitionItems::where('purchase_requisitions_id', $id)->get();
       foreach ($requisiton_items as $value) {
        $product                = ProductsManagement::find($value->product_id);
        $update_requisition_qty = $product->requisition_qty<$value->qty?0:$product->requisition_qty - $value->qty;
        $product->update([
         'requisition_qty' => $update_requisition_qty
        ]);
        $value->delete();
       }

        return redirect()->back()->withSuccess("Purchases Requisition Deleted Successfully");
    }

    public function item_delete($id){
        $requisiton_item        = PurchaseRequisitionItems::where('id', $id)->first();
        $product                = ProductsManagement::find($requisiton_item->product_id);
        $update_requisition_qty = $product->requisition_qty<$requisiton_item->qty?0:$product->requisition_qty - $requisiton_item->qty;
        $product->update([
         'requisition_qty' => $update_requisition_qty
        ]);
        $requisiton_item->delete();

        return response()->json([
            "msg" =>  "Item Delete Successfully"
        ], 200);
    }

    public function get_descriotion_of_good()
    {
        $description_of_good = DB::table('products')->select('id', 'title as text')->get();
        return response()->json($description_of_good);
    }

    private function get_purchase_requisition_no($section)
    {
        $section_code = $section->code.'-';
        $last   = PurchaseRequisitions::where('purchase_requisition_no','LIKE',"%{$section_code}%")
                                        ->orderBy('purchase_requisition_no','DESC')
                                        ->first();
        $number = $last ? explode('-',$last->purchase_requisition_no)[1] : 0;
        
        $req_no = $section->code . '-' . str_pad(($number + 1), 4, 0, STR_PAD_LEFT);
        return $req_no;
    }
    public function purchases_item_data($id){
        $purchase_item = DB::table('purchase_requisition_items')
                            ->join('products','purchase_requisition_items.product_id','products.id')
                            ->selectRaw('
                                        purchase_requisition_items.*,
                                        products.uom_title
                                        ')
                            ->where('purchase_requisitions_id', $id)->get();
        $this->response['purchases_item'] = $purchase_item;
        return response()->json($this->response);

    }

    public function purchases_approve_qty(Request $request){
        DB::beginTransaction();
        // dd($request->all());
        $purchasesItemDAta = $request->item;

        $grand_total = 0;
        foreach ($purchasesItemDAta as $item) {
            $purchases_item = PurchaseRequisitionItems::where('id', $item['item_id'])->first();

            if($item['approve_qty'] != null){
                $purchases_item->update([
                    "qty_approves" => $purchases_item->qty_approves + $item['approve_qty'],
                    "comment" =>$item['comment']
                ]);
            }else{
                $purchases_item->update([
                    "qty_approves" => $purchases_item->qty,
                    "comment" =>$item['comment']
                ]);
            };
            $grand_total += ($purchases_item->qty_approves) * ($purchases_item->unit_rate);
        }
        $purchases_items = PurchaseRequisitionItems::where('purchase_requisitions_id', $purchases_item->purchase_requisitions_id)->get();
        foreach ($purchases_items as $value) {
           $product =  ProductsManagement::find($value->product_id);
           $adjust_request_qty = $product->requisition_qty<$value->qty?0:$product->requisition_qty - $value->qty;
           $update_request_qty = $adjust_request_qty + $value->qty_approves;
           $product->update([
            'requisition_qty' => $update_request_qty
           ]);
        }
        $purchase_requisitions_id = PurchaseRequisitionItems::find($request->item[0]['item_id'])->purchase_requisitions_id;

        PurchaseRequisitions::where('id', $purchase_requisitions_id)->update([
            'authorized' => Auth::user()->id,
            'grand_total' => $grand_total,
            'authorized_at' => now('Asia/Dhaka'),
            'rejection'      => 0,
            'reject_comment' => null,
            'reject_user_id' => null,
            'reject_at'      => null,
        ]);
        
        DB::commit();
        return response()->json('Requisition Approved Successfully');
    
    }

    public function purchases_item_qty($id){
        $purchases_item = PurchaseRequisitionItems::where('purchase_requisitions_id', $id)->get();
        $this->response['purchases_item'] = $purchases_item;
        return response()->json($this->response);

    }

    public function purchases_updated_qty(Request $request){
        
        DB::beginTransaction();
        $purchases_item = $request->item;
        foreach ($purchases_item as $item) {
            $purchases_item = PurchaseRequisitionItems::where('id', $item['item_id'])->first();
            $purchases_item->update([
                "qty" => $item['qty']
            ]);
        }

        DB::commit();


        return response()->json('Requisition Approved Successfully');
    }

    public function purchases_requisition_pdf($id)
    {
        $purchaseRequisitions = PurchaseRequisitions::where('id', $id)->first();

        $purchaseRequisitions->data = PurchaseRequisitions::where('id', $id)
        ->first();
        $purchaseRequisitions->items = PurchaseRequisitionItems::where('purchase_requisitions_id', $purchaseRequisitions->id)->get();

        if ($purchaseRequisitions->authorized) {
            $purchaseRequisitions->authorize = Signature::where('user_id', $purchaseRequisitions->authorized)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->created_by) {
            $purchaseRequisitions->created_by = Signature::where('user_id', $purchaseRequisitions->created_by)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->agmDgmGm_verified_at) {
            $purchaseRequisitions->agmGmDgm = Signature::where('user_id', $purchaseRequisitions->agm_dgm_gm_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->purchase_verified_at) {
            $purchaseRequisitions->purchase = Signature::where('user_id', $purchaseRequisitions->purchase_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->accounts_verified_at) {
            $purchaseRequisitions->accounts = Signature::where('user_id', $purchaseRequisitions->accounts_user)->first()->signature ?? '';
        }

        $totalQty = 0;
        $totalApprovedQty = 0;
        $totalRate = 0;
        foreach ($purchaseRequisitions->items as $value) {
            $totalQty += (int)$value->qty; 
            $totalApprovedQty += (int)$value->qty_approves;  
            $totalRate += (float)$value->unit_rate;  
        }

        $purchaseRequisitions->totalQty = $totalQty;
        $purchaseRequisitions->totalApprovedQty = $totalApprovedQty;
        $purchaseRequisitions->totalUnitRate = $totalRate;
        $view = view('purchaserequisition::purchase.pdf', compact('purchaseRequisitions'))->render();

        $mpdf = new \Mpdf\Mpdf();
        //$mpdf->AddPage('L');
        $mpdf->WriteHTML($view);
        $mpdf->Output('Purchase-requisition'. time() . ".pdf", "I");
    }

    public function purchases_requisition_preview($id)
    {
        $purchaseRequisitions = PurchaseRequisitions::where('id', $id)
                                                ->first();
        $purchaseRequisitions->data = PurchaseRequisitions::where('id', $id)
                                                ->first();
            

        $purchaseRequisitions->items = PurchaseRequisitionItems::where('purchase_requisitions_id', $purchaseRequisitions->id)->get();
        
        if ($purchaseRequisitions->authorized) {
            $purchaseRequisitions->authorize = Signature::where('user_id', $purchaseRequisitions->authorized)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->created_by) {
            $purchaseRequisitions->created_by = Signature::where('user_id', $purchaseRequisitions->created_by)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->purchase_verified_at) {
            $purchaseRequisitions->purchase = Signature::where('user_id', $purchaseRequisitions->purchase_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->accounts_verified_at) {
            $purchaseRequisitions->accounts = Signature::where('user_id', $purchaseRequisitions->accounts_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->agmDgmGm_verified_at) {
            $purchaseRequisitions->agmGmDgm = Signature::where('user_id', $purchaseRequisitions->agm_dgm_gm_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->ed_verified_at) {
            $purchaseRequisitions->ed = Signature::where('user_id', $purchaseRequisitions->ed_user)->first()->signature ?? '';
        }
        

        $totalQty = 0;
        $totalApprovedQty = 0;
        $totalRate = 0;
        $GrandTotalRate = 0;
        foreach ($purchaseRequisitions->items as $value) {
            $totalQty += (int)$value->qty; 
            $totalApprovedQty += (int)$value->qty_approves;  
            $totalRate += (float)$value->unit_rate;  
            $GrandTotalRate += (float)$value->unit_rate * (int)$value->qty_approves;
        }

        $purchaseRequisitions->totalQty = $totalQty;
        $purchaseRequisitions->totalApprovedQty = $totalApprovedQty;    
        $purchaseRequisitions->totalUnitRate = $totalRate;
        $purchaseRequisitions->GrandTotalRate = $GrandTotalRate;

        return view('purchaserequisition::purchase.preview', compact('purchaseRequisitions'));
    }
    public function po_budget_preview($id)
    {
        $purchaseRequisitions        = PurchaseRequisitions::where('id', $id)->first();
        $purchaseRequisitions->data  = PurchaseRequisitions::where('id', $id)->first();
        $purchaseRequisitions->items = DB::table('purchase_requisition_items')
        ->leftJoin('purchase_work_order_items', 'purchase_work_order_items.pur_req_item_id', 'purchase_requisition_items.id')
        ->leftJoin('purchase_budget_items', 'purchase_budget_items.pur_req_item_id', 'purchase_requisition_items.id')
        ->where('purchase_requisition_items.purchase_requisitions_id', $purchaseRequisitions->id)
        ->where(function ($query){
            $query->where('purchase_requisition_items.is_work_order',1)
                ->orWhere('purchase_requisition_items.is_budget',1);
        })
        ->selectRaw('
            purchase_requisition_items.*,
            purchase_work_order_items.work_order_no,
            purchase_budget_items.budget_no,
            purchase_work_order_items.pur_req_item_id as pur_req_item_id_in_po,
            purchase_budget_items.pur_req_item_id as pur_req_item_id_in_budget
            
        ')
        ->get();
        
        if ($purchaseRequisitions->authorized) {
            $purchaseRequisitions->authorize = Signature::where('user_id', $purchaseRequisitions->authorized)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->created_by) {
            $purchaseRequisitions->created_by = Signature::where('user_id', $purchaseRequisitions->created_by)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->purchase_verified_at) {
            $purchaseRequisitions->purchase = Signature::where('user_id', $purchaseRequisitions->purchase_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->accounts_verified_at) {
            $purchaseRequisitions->accounts = Signature::where('user_id', $purchaseRequisitions->accounts_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->agmDgmGm_verified_at) {
            $purchaseRequisitions->agmGmDgm = Signature::where('user_id', $purchaseRequisitions->agm_dgm_gm_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->ed_verified_at) {
            $purchaseRequisitions->ed = Signature::where('user_id', $purchaseRequisitions->ed_user)->first()->signature ?? '';
        }
        

        $totalQty         = 0;
        $totalApprovedQty = 0;
        $totalRate        = 0;
        $GrandTotalRate   = 0;
        foreach ($purchaseRequisitions->items as $value) {
            $totalQty         += (int)$value->qty;
            $totalApprovedQty += (int)$value->qty_approves;
            $totalRate        += (float)$value->unit_rate;
            $GrandTotalRate   += (float)$value->unit_rate * (int)$value->qty_approves;
        }

        $purchaseRequisitions->totalQty           = $totalQty;
        $purchaseRequisitions->totalApprovedQty   = $totalApprovedQty;
        $purchaseRequisitions->totalUnitRate      = $totalRate;
        $purchaseRequisitions->GrandTotalRate     = $GrandTotalRate;
        $purchaseRequisitions->po_budget_complete = 'true';
        return view('purchaserequisition::purchase.preview', compact('purchaseRequisitions'));
    }

    public function po_budget_pdf($id)
    {
        $purchaseRequisitions = PurchaseRequisitions::where('id', $id)->first();

        $purchaseRequisitions->data = PurchaseRequisitions::where('id', $id)
        ->first();
        $purchaseRequisitions->items = PurchaseRequisitionItems::where('purchase_requisitions_id', $purchaseRequisitions->id)
                                                ->where(function ($query){
                                                    $query->where('purchase_requisition_items.is_work_order',1)
                                                        ->orWhere('purchase_requisition_items.is_budget',1);
                                                })
                                                ->get();

        if ($purchaseRequisitions->authorized) {
            $purchaseRequisitions->authorize = Signature::where('user_id', $purchaseRequisitions->authorized)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->created_by) {
            $purchaseRequisitions->created_by = Signature::where('user_id', $purchaseRequisitions->created_by)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->agmDgmGm_verified_at) {
            $purchaseRequisitions->agmGmDgm = Signature::where('user_id', $purchaseRequisitions->agm_dgm_gm_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->purchase_verified_at) {
            $purchaseRequisitions->purchase = Signature::where('user_id', $purchaseRequisitions->purchase_user)->first()->signature ?? '';
        }

        if ($purchaseRequisitions->accounts_verified_at) {
            $purchaseRequisitions->accounts = Signature::where('user_id', $purchaseRequisitions->accounts_user)->first()->signature ?? '';
        }

        $totalQty = 0;
        $totalApprovedQty = 0;
        $totalRate = 0;
        foreach ($purchaseRequisitions->items as $value) {
            $totalQty += (int)$value->qty; 
            $totalApprovedQty += (int)$value->qty_approves;  
            $totalRate += (float)$value->unit_rate;  
        }

        $purchaseRequisitions->totalQty = $totalQty;
        $purchaseRequisitions->totalApprovedQty = $totalApprovedQty;
        $purchaseRequisitions->totalUnitRate = $totalRate;
        $view = view('purchaserequisition::purchase.pdf', compact('purchaseRequisitions'))->render();

        $mpdf = new \Mpdf\Mpdf();
        //$mpdf->AddPage('L');
        $mpdf->WriteHTML($view);
        $mpdf->Output('Purchase-requisition'. time() . ".pdf", "I");
    }

    public function purchase_req_for_gate_pass(Request $request)
    {
        if($request->gate_pass){
            $purchaseRequisitions = PurchaseRequisitions::where('id', $request->purchases_req_id)->first();
            $purchases_requisition_items = PurchaseRequisitionItems::whereIn('id',$request->item_id)->get();
            return view('purchaserequisition::purchase.gate-pass-create', compact('purchaseRequisitions', 'purchases_requisition_items'));
        }
    }

    public function get_requisition(){

        return view('purchaserequisition::purchase.purchases-requisitions-report', ['purchase_requisition' => $this->purchase_requisition_report]);
    }

    public function approved_requisition(){
        return view('purchaserequisition::purchase.approved-user-requisitions');
    }
    public function all_requisition(){
        $purchase_requisitions = PurchaseRequisitions::
                                                join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                                ->selectRaw('
                                                    purchase_requisitions.*,
                                                    group_concat(purchase_requisition_items.product_title) as item_title
                                                ')
                                                ->groupBy('purchase_requisitions.purchase_requisition_no')
                                                ->orderBy('purchase_requisitions.id','DESC')
                                                ->get();

        // dd($purchase_requisitions);    
        return view('purchaserequisition::purchase.all-requisitions', compact('purchase_requisitions'));
    }
    public function user_wise_approved_report(){
        $data = PurchaseRequisitions::join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                            ->selectRaw('
                                                purchase_requisitions.*,
                                                group_concat(purchase_requisition_items.product_title, "(",round(purchase_requisition_items.qty),")") as product_title
                                                ')
                                            ->where('purchase_requisitions.created_by', request()->user)
                                            ->groupBy('purchase_requisitions.id')
                                            ->get();

        return view('purchaserequisition::purchase.approved-user-requisitions', compact('data'));
    }
    

    public function store_forwarded_data(Request $request)
    {
        // dd('hi');
        $user_id = auth()->user()->id;
        $roles = $this->user_auth( $user_id );
        
        $requisition = PurchaseRequisitions::find($request->purchases_req_id);
        // dd($requisition);
        if($roles == 'guest' || $roles == 'super-admin' && $requisition->agm_dgm_gm_user == null){
            $requisition->update([
                                'fw_bac_comment'    => null,
                                'fw_form_user_id'   => null,
                                'fw_bac_at'         => null,
                                'fw_to_user_id'     => null,
                                'agm_dgm_gm_user'   => $request->agm_dgm_gm,
                                'user_fw_at'   =>  now('Asia/Dhaka'),
                                ]);
        }elseif($roles == 'DGM' || $roles == 'super-admin' && $requisition->purchase_user == null){
            $requisition->update([
                                'fw_bac_comment'    => null,
                                'fw_form_user_id'   => null,
                                'fw_bac_at'         => null,
                                'fw_to_user_id'     => null,
                                'purchase_user'     => $request->purchase_user,
                                'agm_dgm_gm_fw_at'   => now('Asia/Dhaka'),
                                ]);
        }elseif($roles == 'purchase' || $roles == 'super-admin' && $requisition->accounts_user == null){
            $requisition->update([
                                 'fw_bac_comment'    => null,
                                 'fw_form_user_id'   => null,
                                 'fw_bac_at'         => null,
                                 'fw_to_user_id'     => null,
                                 'accounts_user'     => $request->accounts_user,
                                 'purchase_fw_at'   => now('Asia/Dhaka'),
                                ]);
        }elseif($roles == 'Accounts' || $roles == 'super-admin' && $requisition->authorized == null){
            $requisition->update([
                                'fw_bac_comment'    => null,
                                'fw_form_user_id'   => null,
                                'fw_bac_at'         => null,
                                'fw_to_user_id'     => null,
                                'authorized'        => $request->authorized,
                                'accounts_fw_at'   => now('Asia/Dhaka'),
                                ]);
        }
        if ($roles == 'super-admin') {
            return redirect()->route('pending.requisition')->withSuccess('Forwarded Successfully');
        }else{
            return redirect()->route('purchase.index')->withSuccess('Forwarded Successfully');
        }
    }

    public function purchase_comment($id, $purchase_comments){
        try {
            PurchaseRequisitions::find($id)->update([
                'purchase_comments' => $purchase_comments,
            ]);
            return response()->json([
                "msg"  => "Purchase Comment Update Successfully "
            ], 200);
        } catch (Exception $e) {
            $e->getMessage();
            $e->getLine();
        }
        

    }

    public function get_forward_view($id){
        $purchase_show = PurchaseRequisitions::where('id', $id)->with('purchase_items')->first();
        return view('purchaserequisition::purchase.forward', compact('purchase_show'));
    }
    
    public function get_store_user(){
        $role_id = DB::table('system_roles')->where('name', 'Store')->pluck('id')->toArray();

        $store_user = DB::table('users')->where("active_role_id", 9)
                                        ->selectRaw('id, name as text')
                                        ->get();
        return response()->json($store_user);
    }

    public function get_purchase_user(){
        $role_id = DB::table('system_roles')
                            ->where('name', 'Purchase')
                            ->pluck('id')->toArray();

        $user = DB::table('users')->whereIn("active_role_id", $role_id)
                                    ->selectRaw('id, name as text')
                                    ->get();
        return response()->json($user);
    }

    public function get_agm_dgm_gm_user(){
        $role_id = DB::table('system_roles')
                                ->where('name', 'AGM')
                                ->orWhere('name', 'DGM')
                                ->orWhere('name', 'GM')
                                ->pluck('id')->toArray();

        $user = DB::table('users')->whereIn("active_role_id", $role_id)
                                    ->selectRaw('id, name as text')
                                    ->get();
        return response()->json($user);
    }

    public function get_accounts_user(){
        $role_id = DB::table('system_roles')
                            ->where('name', 'Accounts')
                            ->pluck('id')->toArray();

        $user = DB::table('users')->where("active_role_id", $role_id)
                                    ->selectRaw('id, name as text')
                                    ->get();
        return response()->json($user);
    }

    public function get_authorize_user(){
                $role_id = DB::table('system_roles')
                                ->where('name', 'Authorized')
                                ->pluck('id')
                                ->toArray();
                                    
                $user = DB::table('users')
                            ->where("active_role_id", $role_id)
                            ->selectRaw('id, name as text')
                            ->get();
        return response()->json($user);
    }

    public function userApprove()
    {
        try{
            $user = request()->all();
           
            if($user['value'] == "purchase_user"){
                PurchaseRequisitions::find($user['id'])->update([
                    "purchase_verified_at" => now('Asia/Dhaka')
                ]);
            }
            else if($user['value'] == "agm_dgm_gm_user"){
                PurchaseRequisitions::find($user['id'])->update([
                    "agmDgmGm_verified_at" => now('Asia/Dhaka')
                ]);
            }
            else if($user['value'] == "accounts_user"){
                PurchaseRequisitions::find($user['id'])->update([
                    "accounts_verified_at" => now('Asia/Dhaka')
                ]);
            }
            else{
                throw new Exception('Your are not authorized!', "403");
            }

            return response()->json([
                "msg" => "Requisition Approved Successfully",
                'data' => date('d-M-Y',strtotime(now())),

            ]);
        } catch (\Exception $e) {

            return response()->json([
                "msg"  => $e->getMessage(),
                "line" => $e->getLine()
            ], 400);
        }
        
    }

    public function forwardApproved(){
        $user = request()->all();
        try {
            if ($user['value'] == "it") {
                PurchaseRequisitions::find($user['id'])->update([
                    "approved_at" => now('Asia/Dhaka'),
                    "approved_by" => Auth::user()->id
                ]);
            } 
            return response()->json([
                "msg" => "Requisition Approved Successfully",

            ]);
        } catch (Exception $e) {
            return response()->json([
                "msg"  => $e->getMessage(),
                "line" => $e->getLine()
            ], 400);
        }
    }
    public function forward_back_user($id){
        $user_id = auth()->user()->id;
        $roles = $this->user_auth( $user_id );

        if($roles == 'DGM'){
                $forward_user = DB::table('purchase_requisitions')->where('id', $id)
                                ->selectRaw('
                                purchase_requisitions.created_by
                                ')->first();

            }elseif($roles == 'purchase'){
                $forward_user = DB::table('purchase_requisitions')->where('id', $id)
                                ->selectRaw('
                                purchase_requisitions.agm_dgm_gm_user
                                ')->first();

            }elseif($roles == 'Accounts'){
                $forward_user = DB::table('purchase_requisitions')->where('id', $id)
                                ->selectRaw('
                                purchase_requisitions.purchase_user
                                ')->first();

            }elseif($roles == 'authorized'){
                $forward_user = DB::table('purchase_requisitions')->where('id', $id)
                                ->selectRaw('
                                purchase_requisitions.accounts_user
                                ')->first();
            }
                $user=(array)$forward_user;
                $user_name = [];
                foreach ($user as $value) {
                    $user_name[] = DB::table('users')
                                    ->where('id', $value)
                                    ->selectRaw('
                                    users.id,
                                    users.name as text
                                    ')->first();
                }
        return response()->json($user_name);
    }

    public function forward_back(Request $request, $id){
       $user_id = auth()->user()->id;
       $roles = $this->user_auth( $user_id );
        
        if($roles == 'DGM'){
            PurchaseRequisitions::where('id',$id)
                                    ->update([
                                            'fw_bac_comment' => $request->comment,
                                            'fw_form_user_id' => auth()->user()->id,
                                            'fw_to_user_id' => $request->fw_user_id,
                                            'agm_dgm_gm_user' => null,
                                            'agmDgmGm_verified_at' => null,
                                            'fw_bac_at' =>now('Asia/Dhaka'),
                                            ]);
        }elseif($roles == 'purchase'){
            PurchaseRequisitions::where('id',$id)
                                    ->update([
                                        'fw_bac_comment' => $request->comment,
                                        'fw_form_user_id' => auth()->user()->id,
                                        'fw_to_user_id' => $request->fw_user_id,
                                        'agmDgmGm_verified_at' => null,
                                        'purchase_user' => null,
                                        'purchase_verified_at' => null,
                                        'fw_bac_at' => now('Asia/Dhaka'),
                                    ]);
        }elseif($roles == 'Accounts'){
            PurchaseRequisitions::where('id', $id)
                                    ->update([
                                             'fw_bac_comment' => $request->comment,
                                             'fw_form_user_id' => auth()->user()->id,
                                             'fw_to_user_id' => $request->fw_user_id,
                                             'accounts_user' => null,
                                             'accounts_verified_at' => null,
                                             'purchase_verified_at' => null,
                                             'fw_bac_at' => now('Asia/Dhaka'),
                                            ]);
        }elseif($roles == 'authorized'){
             PurchaseRequisitions::where('id', $id)
                                    ->update([
                                             'fw_bac_comment' => $request->comment?? 'Unfortunately Forward Is back!',
                                             'fw_form_user_id' => auth()->user()->id,
                                             'fw_to_user_id' => $request->fw_user_id,
                                             'fw_bac_at' => now('Asia/Dhaka'),
                                             'accounts_verified_at' => null,
                                             'authorized' => null,
                                             'rejection'      => 0,
                                             'reject_comment' => null,
                                             'reject_user_id' => null,
                                             'reject_at'      => null,
                                            ]);
        }
            return response()->json([
                            "msg" => "Forward Back Successfully",
                    ]);
    }

    public function requisition_rejection(Request $request, $id){
        try {
                PurchaseRequisitions::where('id', $id)
                                    ->update([
                                        'rejection' => 1 ,
                                        'reject_comment' =>  $request->comment,
                                        'reject_user_id' =>  auth()->user()->id,
                                        'reject_at' => now('Asia/Dhaka'),
                                    ]);
            $path ='/purchase-requisitions/pending-requisition/' ;

            return response()->json([
                    "msg"  => "Purchase Requisition Reject Successfully ",
                    "path" => $path
                    ], 200);
                
        } catch (Exception $e) {
            return response()->json([
                    "msg"  => $e->getMessage(),
                    "line" => $e->getLine()
                    ], 400);
        }
    }

    public function requisition_rejection_restore($id){
        PurchaseRequisitions::where('id', $id)
                            ->update([
                                'rejection'      => 0,
                                'reject_comment' => null,
                                'reject_user_id' => null,
                                'reject_at'      => null,
                            ]);
        return redirect()->back()->withSuccess('Requisition Restore Successfully');
    }

    public function requisition_rejection_list(){
        $user_id = auth()->user()->id;
        $roles = $this->user_auth( $user_id );

        if ($roles == 'super-admin') {
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'authorized') {
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();

        }elseif($roles == 'DGM'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'Accounts'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'purchase'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }elseif($roles == 'view'){
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }else{
            $purchase_requisitions = DB::table('purchase_requisitions')
                                        ->join('purchase_requisition_items','purchase_requisition_items.purchase_requisitions_id','purchase_requisitions.id')
                                        ->where('purchase_requisitions.created_by', auth()->user()->id)
                                        ->where('purchase_requisitions.rejection', '=', 1)
                                        ->selectRaw('
                                        purchase_requisitions.*,
                                        group_concat(purchase_requisition_items.product_title) as item_title,
                                        round(sum(purchase_requisition_items.qty_approves * purchase_requisition_items.unit_rate), 2) as grand_total
                                        ')
                                        ->groupBy('purchase_requisitions.purchase_requisition_no')
                                        ->orderBy('purchase_requisitions.id','DESC')
                                        ->get();
        }

        return view('purchaserequisition::purchase.rejection', compact('purchase_requisitions'));
    }

    public function summary(){
        return view('purchaserequisition::summary');
    }

    public function get_product_title(){
        $products = ProductsManagement::all();
        $data = [];
        foreach ($products as $product) 
        {
            $data[] = [
                'id'   => $product->id,
                'text' => $product->title . '(' . $product->stock_qty . ')'
            ];
        }
        return response()->json($data);
    }

    public function get_product_data($id) {
        $product_info = ProductsManagement::where('id', $id)->first();
        return response()->json($product_info);
    }

    public function get_unit_select(){
        $section = DB::table('units')->select('id', 'title as text')->get();
        return response()->json($section);

    }
    public function  get_section_select($id){
        $section = DB::table('sections')->where('unit_id', $id)->select('id', 'section_title as text')->get();
        return response()->json($section);
    }
    
}
