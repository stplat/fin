<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Dkre;
use App\Models\ActivityType;
use App\Models\Period;


class InvolvementsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $dkres = Dkre::get()->pluck('id');
    $periods = Period::get()->pluck('id');
    $activities = ActivityType::get()->pluck('id');
    $data = [];

    foreach ($dkres as $dkre) {
      foreach ($periods as $period) {
        foreach ($activities as $activity) {
          $arr = [];
          $arr['period_id'] = $period;
          $arr['dkre_id'] = $dkre;
          $arr['activity_type_id'] = $activity;
          $arr['payment_balance_article_id'] = 19;
          $arr['version_id'] = 1;
          $arr['involve_by_prepayment_last_year'] = 1000;
          $arr['involve_by_prepayment_current_year'] = 200;
          $arr['involve_by_turnover'] = 100;
          $arr['prepayment_current_year'] = 30;
          $arr['prepayment_next_year'] = 10;
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($data, $arr);
        }
      }
    }

    DB::table('involvements')->insert($data);
  }
}
