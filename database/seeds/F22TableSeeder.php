<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

require_once __DIR__ . '/phpexcel/PHPExcel.php';
require_once __DIR__ . '/phpexcel/PHPExcel/Writer/Excel2007.php';
require_once __DIR__ . '/phpexcel/PHPExcel/IOFactory.php';

class F22TableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $excel = PHPExcel_IOFactory::load(__DIR__ . '/f22.xlsx');
    $P16 = $excel->getActiveSheet()->getCell('P16');

    DB::table('f22')->insert([
      [
        'article_id' => '1',
        'period_id' => '1',
        'activity_id' => '1',
        'sum' => $P16,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);
  }
}
