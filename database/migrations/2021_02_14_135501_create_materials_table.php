<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
//    Schema::create('materials', function (Blueprint $table) {
//      $table->id();
//      $table->integer('period_id');
//      $table->integer('dkre_id');
//      $table->string('code');
//      $table->string('name');
//      $table->string('size');
//      $table->string('gost');
//      $table->string('type');
//      $table->string('unit');
//      $table->double('quantity');
//      $table->string('price');
//      $table->string('total');
//      $table->double('unused')->nullable();
//      $table->timestamps();
//    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
//    Schema::dropIfExists('materials');
  }
}
