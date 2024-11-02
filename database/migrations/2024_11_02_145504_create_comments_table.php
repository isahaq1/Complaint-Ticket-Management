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
        Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->bigInteger('complaint_id')->unsigned()->index()->nullable();
        $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        $table->text('comment');
        $table->foreignId('user_id');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
