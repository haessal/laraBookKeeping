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
        Schema::create('bk2_0_account_groups', function (Blueprint $table) {
            $table->uuid('account_group_id')->primary();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('book_id')->on('bk2_0_books');
            $table->enum('account_type', ['asset', 'liability', 'expense', 'revenue']);
            $table->string('account_group_title', 40);
            $table->unsignedBigInteger('bk_uid')->nullable();
            $table->unsignedBigInteger('account_group_bk_code')->nullable();
            $table->boolean('is_current');
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
        Schema::dropIfExists('bk2_0_account_groups');
    }
};
