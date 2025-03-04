<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
        Schema::table('transaction_user', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('transaction_user', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
