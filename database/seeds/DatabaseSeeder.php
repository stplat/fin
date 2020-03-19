<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call(ActivityTableSeeder::class);
    $this->call(ArticleTableSeeder::class);
    $this->call(DkreTableSeeder::class);
    $this->call(F22TableSeeder::class);
    $this->call(PeriodTableSeeder::class);
    $this->call(SupplyTableSeeder::class);
  }
}
