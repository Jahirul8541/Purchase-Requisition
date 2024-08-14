<?php

use Illuminate\Support\Facades\Route;

use Btal\PurchaseRequisition\Http\Controllers\Api\PurchaseRequisitionsReportApiController;

Route::group(['middleware' => ['web','auth', 'authorize']], function (){
    
    Route::post('/report/purchase-requisition-api', [PurchaseRequisitionsReportApiController::class, 'report']);

});
