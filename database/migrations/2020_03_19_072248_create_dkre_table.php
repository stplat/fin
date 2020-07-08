<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDkreTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('dkre', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('be');
      $table->string('slug_name');
      $table->string('name');
      $table->string('slug_zavod');
      $table->string('zavod');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('dkre');
  }
}
