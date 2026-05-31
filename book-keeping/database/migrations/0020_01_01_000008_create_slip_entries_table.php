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
        Schema::create('bk2_0_slip_entries', function (Blueprint $table) {
            $table->uuid('slip_entry_id')->primary();
            $table->uuid('slip_id');
            $table->foreign('slip_id')->references('slip_id')->on('bk2_0_slips');
            $table->uuid('debit');
            $table->foreign('debit')->references('account_id')->on('bk2_0_accounts');
            $table->uuid('credit');
            $table->foreign('credit')->references('account_id')->on('bk2_0_accounts');
            $table->bigInteger('amount');
            $table->string('client', 40);
            $table->string('outline', 200);
            $table->uuid('credit_card_statement_id')->nullable();
            $table->foreign('credit_card_statement_id')->references('credit_card_statement_id')->on('bk2_0_credit_card_statements');
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
        Schema::dropIfExists('bk2_0_slip_entries');
    }
};
