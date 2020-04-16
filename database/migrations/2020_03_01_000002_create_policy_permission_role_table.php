<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyPermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_permission_role', function (Blueprint $table) {
            $table->bigIncrements('id');  
            $table->unsignedBigInteger('policy_role_id');  
            $table->unsignedBigInteger('policy_permission_id');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_permission_role');
    }
}
