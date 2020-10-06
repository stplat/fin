<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('finances', function (Blueprint $table) {
      $table->id();
      $table->integer('period_id');
      $table->integer('activity_type_id');
      $table->integer('source_id');
      $table->integer('payment_balance_article_sub_general');
      $table->integer('version_id');
      $table->double('count');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('finances');
  }
}
