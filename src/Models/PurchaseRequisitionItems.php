<?php

namespace Btal\PurchaseRequisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Btal\Productmanagement\Models\Products_Management;
class PurchaseRequisitionItems extends Model
{
    use HasFactory;
    protected $guarded = [];
    // protected $table = 'purchase_requisition_items';
    public function purchase(){
        return $this->BelongsTo(PurchaseRequisitions::class);
    }

    public function products(){
        return $this->hasMany(ProductsManagement::class, 'product_management_id', 'product_management_id');
    }
}
