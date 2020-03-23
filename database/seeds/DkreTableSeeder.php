<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DkreTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::table('dkre')->insert([
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'zavod' => 'ДКРЭ_ОКТ_СПБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'zavod' => 'ДКРЭ_ОКТ_МОСК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'zavod' => 'ДКРЭ_ОКТ_НЖГ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3912',
        'name' => 'ДКРЭ_ОКТ',
        'zavod' => 'ДКРЭ_ОКТ_ЯРС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3913',
        'name' => 'ДКРЭ_С-КАВ',
        'zavod' => 'ДКРЭ_С-КАВ_РСТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3914',
        'name' => 'ДКРЭ_Ю-ВОСТ',
        'zavod' => 'ДКРЭ_Ю-ВОСТ_ВРН',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'zavod' => 'ДКРЭ_КБШ_СРТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3915',
        'name' => 'ДКРЭ_КБШ',
        'zavod' => 'ДКРЭ_КБШ_СМР',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'zavod' => 'ДКРЭ_СВЕРД_ЕКТ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3916',
        'name' => 'ДКРЭ_СВЕРД',
        'zavod' => 'ДКРЭ_СВЕРД_ЧЛБ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'zavod' => 'ДКРЭ_З-СИБ_НВС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3917',
        'name' => 'ДКРЭ_З-СИБ',
        'zavod' => 'ДКРЭ_З-СИБ_КРАС',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'zavod' => 'ДКРЭ_ЗАБ_ИРК',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'zavod' => 'ДКРЭ_ЗАБ_ЧТН',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3918',
        'name' => 'ДКРЭ_ЗАБ',
        'zavod' => 'ДКРЭ_ЗАБ_ХБР',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3990',
        'name' => 'ДКРЭ соб.',
        'zavod' => 'ДКРЭ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'be' => '3991',
        'name' => 'ДКРЭ_ЭМЗ',
        'zavod' => 'ДКРЭ_ЭМЗ',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);
  }
}
