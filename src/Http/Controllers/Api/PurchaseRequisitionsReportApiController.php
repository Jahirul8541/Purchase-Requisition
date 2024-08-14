<?php

namespace Btal\PurchaseRequisition\Http\Controllers\Api;


use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Btal\PurchaseRequisition\Models\PurchaseRequisitions;

class PurchaseRequisitionsReportApiController extends Controller
{
    private $header = [
        
        "section_title"  => ["Unit Name","unique"],
        "unit_title"  => ["Section","unique"],
        "purchase_requisition_no"  => ["Req No","unique"],
        "contact_person"  => ["Contact Person","unique"],
        "contact_person_phone"  => ["Phone","unique"],
        "product_title"  => ["Item","unique"],
        "total_qty"  => ["Total Qty","sum"],
        "qty_approves"  => ["Approves Qty","sum"],
        "due_qty"  => ["Due Qty","sum"],
        "grand_total"  => ["Total Amount","sum"],

    ];

    public function report()
    {
                  $data = PurchaseRequisitions::join('purchase_requisition_items',
                         'purchase_requisition_items.purchase_requisitions_id',
                         'purchase_requisitions.id')
                        ->selectRaw('
                                     purchase_requisitions.*,
                                     sum(purchase_requisition_items.qty) as total_qty,
                                     sum(purchase_requisition_items.qty_approves) as qty_approves,
                                     round(sum(purchase_requisition_items.qty) - sum(purchase_requisition_items.qty_approves)) as due_qty,
                                     group_concat(purchase_requisition_items.product_title, "(",
                                     round(purchase_requisition_items.qty),")") as product_title
                                    ')
                        ->where('purchase_requisitions.created_by', request()->user)
                        ->groupBy('purchase_requisitions.id')
                        ->get();

                            return response()->json([
                                "header" => $this->header,
                                "data"   => $data
                            ], 200);
    }
}
