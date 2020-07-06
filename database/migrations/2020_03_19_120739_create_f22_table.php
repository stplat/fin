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
      $table->increments('id');
      $table->integer('period_id');
      $table->integer('vid_deyatelnosti_id');
      $table->integer('istochnik_postavki_id');
      $table->integer('statya_pb_id');
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
    Schema::dropIfExists('f22');
  }
}
