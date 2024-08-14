<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppoveFloorReqyuisitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appove_floor_reqyuisition', function (Blueprint $table) {
            $table->id();
            $table->integer('floor_requisition_id')->nullable();
            $table->integer('floor_requisition_product_id')->nullable();
            $table->string('product_title')->nullable();
            $table->float('total_qty')->nullable();
            $table->float('approved_qty')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appove_floor_reqyuisition');
    }
}
