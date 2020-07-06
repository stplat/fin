<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call(VidDeyatelnostiTableSeeder::class);
    $this->call(StatyaPbTableSeeder::class);
    $this->call(DkreTableSeeder::class);
    $this->call(F22TableSeeder::class);
    $this->call(PeriodTableSeeder::class);
    $this->call(IstochnikPostavkiTableSeeder::class);
    $this->call(BudgetTableSeeder::class);
    $this->call(PlanpostavkiTableSeeder::class);
  }
}
