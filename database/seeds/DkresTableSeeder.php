<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DkresTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::table('dkres')->insert([
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_СПБ',
        'area' => 'ОКТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_МОСК',
        'area' => 'МОСК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_НЖГ',
        'area' => 'ГОРЬК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_ЯРС',
        'area' => 'СЕВ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3913',
        'name' => 'ДКРЭ_С-КАВ',
        'region' => 'ДКРЭ_С-КАВ_РСТ',
        'area' => 'С-КАВ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3914',
        'name' => 'ДКРЭ_Ю-ВОСТ',
        'region' => 'ДКРЭ_Ю-ВОСТ_ВРН',
        'area' => 'Ю-ВОСТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'region' => 'ДКРЭ_КБШ_СРТ',
        'area' => 'ПРИВ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'region' => 'ДКРЭ_КБШ_СМР',
        'area' => 'КБШ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'region' => 'ДКРЭ_СВЕРД_ЕКТ',
        'area' => 'СВЕРД',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'region' => 'ДКРЭ_СВЕРД_ЧЛБ',
        'area' => 'Ю-УР',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'region' => 'ДКРЭ_З-СИБ_НВС',
        'area' => 'З-СИБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'region' => 'ДКРЭ_З-СИБ_КРАС',
        'area' => 'КРАС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ИРК',
        'area' => 'В-СИБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ЧТН',
        'area' => 'ЗАБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ХБР',
        'area' => 'ДВОСТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3990',
        'name' => 'ДКРЭ соб.',
        'region' => 'ДКРЭ',
        'area' => 'Собственно',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3991',
        'name' => 'ДКРЭ_ЭМЗ',
        'region' => 'ДКРЭ_ЭМЗ',
        'area' => 'МЭЗ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);
  }
}
