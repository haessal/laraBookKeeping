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
        Schema::create('bk2_0_slip_groups', function (Blueprint $table) {
            $table->uuid('slip_group_id')->primary();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('book_id')->on('bk2_0_books');
            $table->string('slip_group_outline', 200);
            $table->string('slip_group_memo', 500)->nullable();
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
        Schema::dropIfExists('bk2_0_slip_groups');
    }
};
