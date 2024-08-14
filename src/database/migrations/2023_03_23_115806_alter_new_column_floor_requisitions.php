<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNewColumnFloorRequisitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('floor_requisitions', function (Blueprint $table) {
            $table->integer('store_user')->nullable();
            $table->dateTime('store_verified_at')->nullable();
            $table->integer('supervisor')->nullable();
            $table->dateTime('supervisor_verified_at')->nullable();
            $table->integer('production_manager')->nullable();
            $table->dateTime('pm_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('floor_requisitions', function (Blueprint $table) {
            $table->dropColumn('is_construction_update');
            $table->dropColumn('store_user');
            $table->dropColumn('store_verified_at');
            $table->dropColumn('supervisor');
            $table->dropColumn('supervisor_verified_at');
            $table->dropColumn('production_manager');
            $table->dropColumn('pm_verified_at');
        });
    }
}
