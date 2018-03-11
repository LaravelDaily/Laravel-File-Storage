<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1519641977RolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            
if (!Schema::hasColumn('roles', 'price')) {
                $table->decimal('price', 15, 2)->nullable();
                }
if (!Schema::hasColumn('roles', 'stripe_plan_id')) {
                $table->string('stripe_plan_id')->nullable();
                }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('stripe_plan_id');
            
        });

    }
}
