<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentBalanceArticlesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('payment_balance_articles')->insert([
      [
        'general' => '63310',
        'sub_general' => '63310',
        'code' => '63310',
        'sub_general_name' => 'дизельное топливо',
        'name' => 'дизельное топливо',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63320',
        'sub_general' => '63320',
        'code' => '63320',
        'sub_general_name' => 'мазут',
        'name' => 'мазут',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63330',
        'sub_general' => '63330',
        'code' => '63330',
        'sub_general_name' => 'уголь',
        'name' => 'уголь',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63340',
        'sub_general' => '63340',
        'code' => '63340',
        'sub_general_name' => 'бензин и другие виды топлива',
        'name' => 'бензин и другие виды топлива',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63411',
        'sub_general_name' => 'МВСП',
        'name' => 'рельсы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63412',
        'sub_general_name' => 'МВСП',
        'name' => 'стрелочная продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63413',
        'sub_general_name' => 'МВСП',
        'name' => 'рельсовые скрепления',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63414',
        'sub_general_name' => 'МВСП',
        'name' => 'шпалы железобетонные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63415',
        'sub_general_name' => 'МВСП',
        'name' => 'железобетонные изделия, кроме шпалопродукции',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63416',
        'sub_general_name' => 'МВСП',
        'name' => 'шпалы деревянные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63417',
        'sub_general_name' => 'МВСП',
        'name' => 'щебень',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63410',
        'code' => '63418',
        'sub_general_name' => 'МВСП',
        'name' => 'прочие МВСП',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63421',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'запасные части путевой техники',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63422',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'цельнокатанные колеса',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63423',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'локомотивные бандажи',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63424',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'запасные части локомотивов',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63425',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'запасные части моторвагонного подвижного состава',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63420',
        'code' => '63426',
        'sub_general_name' => 'запасные части, узлы и литые детали подвижного состава',
        'name' => 'запасные части вагонов',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63430',
        'code' => '63430',
        'sub_general_name' => 'электротехническая продукция',
        'name' => 'электротехническая продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63440',
        'code' => '63440',
        'sub_general_name' => 'специальная одежда, обувь и средства индивидуальной защиты',
        'name' => 'специальная одежда, обувь и средства индивидуальной защиты',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63450',
        'code' => '63450',
        'sub_general_name' => 'форменная и корпоративная одежда',
        'name' => 'форменная и корпоративная одежда',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63460',
        'code' => '63460',
        'sub_general_name' => 'масла и смазки',
        'name' => 'масла и смазки',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63470',
        'code' => '63470',
        'sub_general_name' => 'периодические печатные издания',
        'name' => 'периодические печатные издания',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63491',
        'sub_general_name' => 'прочие материалы',
        'name' => 'металлопрокат',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63492',
        'sub_general_name' => 'прочие материалы',
        'name' => 'прочие материалы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63493',
        'sub_general_name' => 'прочие материалы',
        'name' => 'химическая продукция',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63494',
        'sub_general_name' => 'прочие материалы',
        'name' => 'лесопиломатериалы',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63495',
        'sub_general_name' => 'прочие материалы',
        'name' => 'продукция машиностроения',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63496',
        'sub_general_name' => 'прочие материалы',
        'name' => 'подшипники вагонные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'general' => '63400',
        'sub_general' => '63490',
        'code' => '63497',
        'sub_general_name' => 'прочие материалы',
        'name' => 'подшипники локомотивные',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ]);

  }
}
