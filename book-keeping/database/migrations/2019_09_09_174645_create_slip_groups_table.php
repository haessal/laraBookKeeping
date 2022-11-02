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
        Schema::create('bk2_0_slip_groups', function (Blueprint $table) {
            $table->uuid('slip_group_id')->primary();
            $table->uuid('book_bound_on');
            $table->foreign('book_bound_on')->references('book_id')->on('bk2_0_books');
            $table->string('slip_group_outline', 200);
            $table->string('slip_group_memo', 500)->nullable();
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
        Schema::dropIfExists('bk2_0_slip_groups');
    }
};
