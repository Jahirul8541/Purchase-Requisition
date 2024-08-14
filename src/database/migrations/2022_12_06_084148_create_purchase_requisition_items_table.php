<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('purchase_requisitions_id');
            $table->integer('product_id');
            $table->string('product_title')->nullable();
            $table->float('qty')->default(0);
            $table->float('stock')->default(0);
            $table->float('qty_approves')->default(0);
            $table->float('unit_rate')->default(0);
            $table->float('total_amount')->default(0);
            $table->string('purpose', 255)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->float('actual_unit_rate')->default(0);
            $table->float('work_order_qty')->default(0);
            $table->string('unit_rate_edit_user', 255)->nullable();
            $table->tinyInteger('is_budget')->default(0);
            $table->float('is_bill_adjust')->default(0);
            $table->string('comment')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('is_challan')->default(0);
            $table->float('challan_qty')->default(0);
            $table->tinyInteger('is_work_order')->default(0);
            $table->string('challan_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
}
