<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('floor_requisition_id')->nullable();
            $table->string('purchase_requisition_no', 255);
            $table->integer('section_id')->nullable();
            $table->string('section_title', 255)->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('unit_title', 255)->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->string('contact_person_phone', 255)->nullable();
            $table->string('order_by', 255)->nullable();
            $table->string('requisition_by_name', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->string('grand_total', 255)->nullable();
            $table->integer('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->integer('store_user')->nullable();
            $table->dateTime('store_verified_at')->nullable();
            $table->integer('hr_admin_user')->nullable();
            $table->dateTime('admin_verified_at')->nullable();
            $table->integer('agm_dgm_gm_user')->nullable();
            $table->dateTime('agmDgmGm_verified_at')->nullable();
            $table->integer('ed_user')->nullable();
            $table->dateTime('ed_verified_at')->nullable();
            $table->integer('ceo_user')->nullable();
            $table->dateTime('ceo_verified_at')->nullable();
            $table->integer('authorized')->nullable();
            $table->dateTime('authorized_at')->nullable();
            $table->integer('rejection')->default(0);
            $table->string('fw_bac_comment')->nullable();
            $table->string('fw_form_user_id')->nullable();
            $table->string('fw_bac_at')->nullable();
            $table->string('reject_comment')->nullable();
            $table->string('reject_at')->nullable();
            $table->string('fw_to_user_id')->nullable();
            $table->string('reject_user_id')->nullable();
            $table->string('purchase_comments')->nullable();
            $table->string('user_fw_at')->nullable();
            $table->string('agm_dgm_gm_fw_at')->nullable();
            $table->string('purchase_fw_at')->nullable();
            $table->string('accounts_fw_at')->nullable();

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
        Schema::dropIfExists('purchase_requisitions');
    }
}
