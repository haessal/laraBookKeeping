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
        Schema::create('bk2_0_accounts', function (Blueprint $table) {
            $table->uuid('account_id')->primary();
            $table->uuid('account_group_id');
            $table->foreign('account_group_id')->references('account_group_id')->on('bk2_0_account_groups');
            $table->string('account_title', 40);
            $table->string('description', 200);
            $table->boolean('selectable');
            $table->boolean('is_credit_card');
            $table->unsignedBigInteger('bk_uid')->nullable();
            $table->unsignedBigInteger('account_bk_code')->nullable();
            $table->bigInteger('display_order')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk2_0_accounts');
    }
};
