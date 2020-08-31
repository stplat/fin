<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call(ActivityTypesTableSeeder::class);
    $this->call(PeriodsTableSeeder::class);
    $this->call(PaymentBalanceArticlesTableSeeder::class);
    $this->call(SourcesTableSeeder::class);
    $this->call(VersionsTableSeeder::class);
    $this->call(DkresTableSeeder::class);

    $this->call(BudgetsTableSeeder::class);
    $this->call(FinancesTableSeeder::class);
    $this->call(InvolvementsTableSeeder::class);
    $this->call(ShipmentsTableSeeder::class);
    $this->call(UsersTableSeeder::class);
  }
}
