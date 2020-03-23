<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanPostavkiTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('plan_postavki', function (Blueprint $table) {
      $table->increments('plan_postavki_id');
      $table->integer('period_id');
      $table->integer('activity_id');
      $table->integer('article_id');
      $table->integer('supply_id');
      $table->integer('dkre_id');
      $table->double('sum');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('plan_postavki');
  }
}
