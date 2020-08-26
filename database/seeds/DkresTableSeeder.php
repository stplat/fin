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
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_МОСК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_НЖГ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'region' => 'ДКРЭ_ОКТ_ЯРС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3913',
        'name' => 'ДКРЭ_С-КАВ',
        'region' => 'ДКРЭ_С-КАВ_РСТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3914',
        'name' => 'ДКРЭ_Ю-ВОСТ',
        'region' => 'ДКРЭ_Ю-ВОСТ_ВРН',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'region' => 'ДКРЭ_КБШ_СРТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'region' => 'ДКРЭ_КБШ_СМР',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'region' => 'ДКРЭ_СВЕРД_ЕКТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'region' => 'ДКРЭ_СВЕРД_ЧЛБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'region' => 'ДКРЭ_З-СИБ_НВС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'region' => 'ДКРЭ_З-СИБ_КРАС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ИРК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ЧТН',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'region' => 'ДКРЭ_ЗАБ_ХБР',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3990',
        'name' => 'ДКРЭ соб.',
        'region' => 'ДКРЭ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3991',
        'name' => 'ДКРЭ_ЭМЗ',
        'region' => 'ДКРЭ_ЭМЗ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);
  }
}
