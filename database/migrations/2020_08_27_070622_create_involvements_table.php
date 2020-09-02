<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvolvementsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('involvements', function (Blueprint $table) {
      $table->id();
      $table->integer('dkre_id');
      $table->integer('period_id');
      $table->integer('activity_type_id');
      $table->integer('payment_balance_article_general');
      $table->integer('version_id');
      $table->double('involve_by_prepayment_last_year');
      $table->double('involve_by_prepayment_current_year');
      $table->double('involve_by_turnover');
      $table->double('prepayment_current_year');
      $table->double('prepayment_next_year');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('involvements');
  }
}
