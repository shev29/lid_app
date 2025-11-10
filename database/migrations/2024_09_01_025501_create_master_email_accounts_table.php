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
        Schema::create('master_email_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('smtp_host');
            $table->string('smtp_port');
            $table->string('smtp_encryption');
            $table->string('username');
            $table->string('password');
            $table->string('display_name');
            $table->tinyInteger('is_active')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_email_accounts');
    }
};
