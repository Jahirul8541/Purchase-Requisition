<?php

use Illuminate\Support\Facades\Route;
use Btal\PurchaseRequisition\Http\Controllers\GatePassController;
use Btal\PurchaseRequisition\Http\Controllers\SummaryDashboardController;
use Btal\PurchaseRequisition\Http\Controllers\FloorRequisitionsController;
use Btal\PurchaseRequisition\Http\Controllers\PurchaseRequisitionsController;

Route::group(['middleware' => ['web','auth', 'authorize']], function () {

    Route::prefix('purchase-requisitions')->group(function()
    {
	Route:: get('/data-sync', [PurchaseRequisitionsController::class, 'data_sync']);
        Route:: get('/', [SummaryDashboardController::class, 'summary'])->name('purchase.summary');
        Route:: get('/summary-data', [SummaryDashboardController::class, 'summary_data'])->name('purchase.summary_data');
        Route:: get('/index', [PurchaseRequisitionsController::class, 'index'])->name('purchase.index');
        Route:: get('/create', [PurchaseRequisitionsController::class, 'create'])->name('purchase.create');
        Route:: post('/store', [PurchaseRequisitionsController::class, 'store'])->name('purchase.store');
        Route:: get('/show/{id}', [PurchaseRequisitionsController::class, 'show'])->name('purchase.show');
        Route:: get('/edit/{id}', [PurchaseRequisitionsController::class, 'edit'])->name('purchase.edit');
        Route:: post('/update', [PurchaseRequisitionsController::class, 'update'])->name('purchase.update');
        Route:: get('/p-user-edit/{id}', [PurchaseRequisitionsController::class, 'p_user_edit'])->name('purchase.p-user-edit');
        Route:: post('/p-user-update', [PurchaseRequisitionsController::class, 'p_user_update'])->name('purchase.p-user-update');
        Route:: delete('/delete/{id}', [PurchaseRequisitionsController::class, 'delete'])->name('purchase.delete');
        Route:: get('/get-descriotion-of-good', [PurchaseRequisitionsController::class, 'get_descriotion_of_good']);
        Route:: get('/purchases-item-data/{id}', [PurchaseRequisitionsController::class, 'purchases_item_data']);
        Route:: post('/purchases-approve-qty', [PurchaseRequisitionsController::class, 'purchases_approve_qty']);
        Route:: get('/purchases-requisition-pdf/{id}', [PurchaseRequisitionsController::class, 'purchases_requisition_pdf'])->name('purchase.pdf');
        Route:: get('/purchases-requisition-preview/{id}', [PurchaseRequisitionsController::class, 'purchases_requisition_preview'])->name('purchase.preview');
        Route:: get('/purchase-req-for-gate-pass', [PurchaseRequisitionsController::class, 'purchase_req_for_gate_pass'])->name('purchase_req_for_gate_pass');
        Route:: get('/get-requisition', [PurchaseRequisitionsController::class, 'get_requisition']);
        Route:: get('/user-wise-requisition-list', [PurchaseRequisitionsController::class, 'user_wise_requisition_list'])->name('user-wise-requisition-list');
        Route:: get('/approved-requisition', [PurchaseRequisitionsController::class, 'approved_requisition']);
        Route:: get('/all-requisition', [PurchaseRequisitionsController::class, 'all_requisition']);
        Route:: get('/user-wise-approved-report', [PurchaseRequisitionsController::class, 'user_wise_approved_report'])->name('user-wise-approved-report');
        Route:: post('/store-forwarded-data', [PurchaseRequisitionsController::class, 'store_forwarded_data'])->name('purchases.saveUser');
        Route:: get('/forward-view/{id}', [PurchaseRequisitionsController::class, 'get_forward_view'])->name('purchases.forwardView');
        Route:: get('/store-user', [PurchaseRequisitionsController::class, 'get_store_user']);
        Route:: get('/purchase-user', [PurchaseRequisitionsController::class, 'get_purchase_user']);
        Route:: get('/agm-dgm-gm-user', [PurchaseRequisitionsController::class, 'get_agm_dgm_gm_user']);
        Route:: get('/accounts-user', [PurchaseRequisitionsController::class, 'get_accounts_user']);
        Route:: get('/authorize', [PurchaseRequisitionsController::class, 'get_authorize_user']);
        Route:: post('/user-wise-approved', [PurchaseRequisitionsController::class, 'userApprove']);
        Route:: post('/user-forward-approved', [PurchaseRequisitionsController::class, 'forwardApproved']);
        Route:: get('/forward-back/{id}', [PurchaseRequisitionsController::class, 'forward_back']);
        Route:: get('/forward-back-user/{id}', [PurchaseRequisitionsController::class, 'forward_back_user']);
        Route:: get('/approved-back/{id}', [PurchaseRequisitionsController::class, 'approved_back']);
        Route:: get('/pending-requisition', [PurchaseRequisitionsController::class, 'pending_requisition'])->name('pending.requisition');
        Route:: post('/requisition-rejection/{id}', [PurchaseRequisitionsController::class, 'requisition_rejection'])->name('requisition.rejection');
        Route:: get('/requisition-rejection-restore/{id}', [PurchaseRequisitionsController::class, 'requisition_rejection_restore'])->name('requisition.restore');
        Route:: get('/requisition-rejection-list', [PurchaseRequisitionsController::class, 'requisition_rejection_list'])->name('requisition.rejectionList');
        Route:: get('/purchase-comment/{id}/{comment}', [PurchaseRequisitionsController::class, 'purchase_comment'])->name('purchase.comment');
        Route:: delete('/item-delete/{id}', [PurchaseRequisitionsController::class, 'item_delete'])->name('item.delete');
        Route::get('/get-product-title', [PurchaseRequisitionsController::class, 'get_product_title']);
        Route::get('/get-product-data/{id}', [PurchaseRequisitionsController::class, 'get_product_data']);
        Route::get('/get-unit', [PurchaseRequisitionsController::class, 'get_unit_select']);
        Route::get('/get-section/{id}', [PurchaseRequisitionsController::class, 'get_section_select']);
        Route::get('/po-budget-complete-list', [PurchaseRequisitionsController::class, 'po_budget_complete_list']);
        Route::get('/po-budget-preview/{id}', [PurchaseRequisitionsController::class, 'po_budget_preview'])->name('purchase.po_budget_preview');
        Route::get('/po-budget-pdf/{id}', [PurchaseRequisitionsController::class, 'po_budget_pdf'])->name('purchase.po_budget_pdf');
        
    });


});


