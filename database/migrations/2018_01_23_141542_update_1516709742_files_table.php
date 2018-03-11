<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1516709742FilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            if(Schema::hasColumn('files', 'filename')) {
                $table->dropColumn('filename');
            }
            if(Schema::hasColumn('files', 'file')) {
                $table->dropColumn('file');
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
        Schema::table('files', function (Blueprint $table) {
                        $table->string('filename')->nullable();
                $table->string('file')->nullable();
                
        });

    }
}
