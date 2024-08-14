<?php

namespace Btal\PurchaseRequisition\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PurchaseRequisitions extends Model
{
    use HasFactory;
    protected $table = 'purchase_requisitions';
    protected $guarded = [];

    public function purchase_items(){
        return $this->hasMany(PurchaseRequisitionItems::class, 'purchase_requisitions_id', 'id');
    }

    public function get_user($id){
        $user = DB::table('users')->find($id);
        return $user;
    }
    
}
