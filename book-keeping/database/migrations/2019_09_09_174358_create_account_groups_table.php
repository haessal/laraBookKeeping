<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_account_groups', function (Blueprint $table) {
            $table->uuid('account_group_id')->primary();
            $table->uuid('book_bound_on');
            $table->foreign('book_bound_on')->references('book_id')->on('bk2_0_books');
            $table->enum('account_type', ['asset', 'liability', 'expense', 'revenue']);
            $table->string('account_group_title', 40);
            $table->unsignedBigInteger('bk_uid')->nullable();
            $table->unsignedBigInteger('bk_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bk2_0_account_groups');
    }
};
