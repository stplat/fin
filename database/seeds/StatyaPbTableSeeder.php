<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StatyaPbTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('statya_pb')->insert([
      [
        'type' => 'топливо',
        'code' => '63310',
        'name' => 'дизельное топливо',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'топливо',
        'code' => '63320',
        'name' => 'мазут',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'топливо',
        'code' => '63330',
        'name' => 'уголь',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'топливо',
        'code' => '63340',
        'name' => 'бензин и другие виды топлива',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63411',
        'name' => 'рельсы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63412',
        'name' => 'стрелочная продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63413',
        'name' => 'рельсовые скрепления',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63414',
        'name' => 'шпалы железобетонные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63415',
        'name' => 'железобетонные изделия, кроме шпалопродукции',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63416',
        'name' => 'шпалы деревянные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63417',
        'name' => 'щебень',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63418',
        'name' => 'прочие МВСП',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63421',
        'name' => 'запасные части путевой техники',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63422',
        'name' => 'цельнокатанные колеса',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63423',
        'name' => 'локомотивные бандажи',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63424',
        'name' => 'запасные части локомотивов',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63425',
        'name' => 'запасные части моторвагонного подвижного состава',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63426',
        'name' => 'запасные части вагонов',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63430',
        'name' => 'электротехническая продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63440',
        'name' => 'специальная одежда, обувь и средства индивидуальной защиты',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63450',
        'name' => 'форменная и корпоративная одежда',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63460',
        'name' => 'масла и смазки',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63470',
        'name' => 'периодические печатные издания',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63491',
        'name' => 'металлопрокат',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63492',
        'name' => 'прочие материалы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63493',
        'name' => 'химическая продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63494',
        'name' => 'лесопиломатериалы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63495',
        'name' => 'продукция машиностроения',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63496',
        'name' => 'подшипники вагонные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'type' => 'материалы',
        'code' => '63497',
        'name' => 'подшипники локомотивные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);

  }
}
