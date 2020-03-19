<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateF22Table extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('f22', function (Blueprint $table) {
      $table->increments('f22_id');
      $table->integer('article_id');
      $table->text('period_id');
      $table->text('activity_id');
      $table->integer('sum');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('f22');
  }
}
