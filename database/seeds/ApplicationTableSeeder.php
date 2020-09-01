<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApplicationTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $periods = \App\Models\Period::all();
    $articles = \App\Models\PaymentBalanceArticle::all();
    $activities = \App\Models\ActivityType::all();
    $versions = \App\Models\Version::all();
    $dkres = \App\Models\Dkre::all();
    $sources = \App\Models\Source::all();
    $data = [];

//    foreach ($periods as $period) {
//      foreach ($articles as $article) {
//        foreach ($activities as $activity) {
//          foreach ($versions as $version) {
//            foreach ($dkres as $dkre) {
//              foreach ($sources as $source) {
//                $arr = [];
//
//                $arr['period_id'] = $period['id'];
//                $arr['payment_balance_article_id'] = $article['id'];
//                $arr['activity_type_id'] = $activity['id'];
//                $arr['version_id'] = $version['id'];
//                $arr['dkre_id'] = $dkre['id'];
//                $arr['source_id'] = $source['id'];
//                $arr['count'] = 0;
//                $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//                $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//                array_push($data, $arr);
//              }
//            }
//          }
//        }
//      }
//    }

    function generator($models)
    {
      foreach ($models as $model) {
        yield $model;
      }
    }

    foreach (generator($periods) as $period) {
      foreach ($articles as $article) {
        foreach ($activities as $activity) {
          foreach ($versions as $version) {
            foreach ($dkres as $dkre) {
              $arr = [];

              $arr['period_id'] = $period['id'];
              $arr['payment_balance_article_id'] = $article['id'];
              $arr['activity_type_id'] = $activity['id'];
              $arr['version_id'] = $version['id'];
              $arr['dkre_id'] = $dkre['id'];
              $arr['source_id'] = 1;
              $arr['count'] = 0;
              $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
              $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

              array_push($data, $arr);
            }
          }
        }
      }
    }


    DB::table('applications')->insert($data);
  }
}
