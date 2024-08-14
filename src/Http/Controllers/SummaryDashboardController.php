<?php

namespace Btal\PurchaseRequisition\Http\Controllers;

use DateTime;
use Exception;
use App\Models\User;
use Nette\Utils\Strings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Pondit\Authorize\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Btal\ReequisitionManagement\Models\Signature;
use Btal\Productmanagement\Models\ProductsManagement;
use Btal\PurchaseRequisition\Models\PurchaseRequisitions;
use Btal\PurchaseRequisition\Models\PurchaseWorkOrderItems;
use Btal\PurchaseRequisition\Models\PurchaseRequisitionItems;

class SummaryDashboardController extends Controller
{
    public static $visiblePermissions = [
        'summary'      => 'Summary Dashboard',
        'summary_data' => 'Summary Dashboard Data',
    ];

   

    public function summary(){
        return view('purchaserequisition::summary');
    }

    public function summary_data(){
        $purchase  = PurchaseRequisitions::get()->count();
        return response()->json($purchase);
    }



    
}
