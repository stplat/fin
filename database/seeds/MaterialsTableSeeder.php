<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Material;

class MaterialsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $excel = IOFactory::load(app()->basePath('database/seeds/assets/materials.xlsx'));
    $excel->setActiveSheetIndex(0);
    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    ExcelParseHelper($data)->each(function ($item) {
      $material = new Material();
      $material->period_id = $item['period_id'];
      $material->dkre_id = $item['dkre_id'];
      $material->code = $item['code'];
      $material->name = $item['name'];
      $material->size = $item['size'];
      $material->gost = $item['gost'];
      $material->type = $item['type'];
      $material->unit = $item['unit'];
      $material->quantity = $item['quantity'];
      $material->price = $item['price'];
      $material->total = $item['total'];
      $material->unused = $item['unused'];
      $material->created_at = now();
      $material->updated_at = now();
      $material->save();
    });
  }
}
