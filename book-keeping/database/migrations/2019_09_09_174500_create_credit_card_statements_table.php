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
        Schema::create('bk2_0_credit_card_statements', function (Blueprint $table) {
            $table->uuid('credit_card_statement_id')->primary();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('book_id')->on('bk2_0_books');
            $table->string('credit_card_statement_outline', 200);
            $table->string('credit_card_statement_memo', 500)->nullable();
            $table->date('date');
            $table->bigInteger('display_order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk2_0_credit_card_statements');
    }
};
