<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentBalanceArticlesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('payment_balance_articles', function (Blueprint $table) {
      $table->id();
      $table->string('general');
      $table->string('sub_general');
      $table->string('code');
      $table->string('sub_general_name');
      $table->string('name');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('payment_balance_articles');
  }
}
