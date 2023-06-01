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
            $table->uuid('book_id');
            $table->foreign('book_id')->references('book_id')->on('bk2_0_books');
            $table->string('slip_group_outline', 200);
            $table->string('slip_group_memo', 500)->nullable();
            $table->bigInteger('display_order')->nullable();
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
