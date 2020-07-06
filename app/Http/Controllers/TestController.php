<?php

namespace App\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer;

class TestController extends Controller {
  public function index() {

    $file = '4';

    $excel = IOFactory::load($_SERVER['DOCUMENT_ROOT'] . '/app/Http/controllers/' . $file . '.xlsx');

    function getCell($excel, $cell) {
      $cell = $excel->getActiveSheet()->getCell($cell);
      return $cell;
    }

    /*$maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A4:' . $maxCell['column'] . $maxCell['row']);*/
    $sheets = [];

    /*echo $excel->getActiveSheet()->getStyle('B6')
      ->getFont()->getUnderline();*/

    $layout = IOFactory::load(__DIR__ . '/layout.xlsx');
    $sheet = $layout->getActiveSheet();
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($layout);

    $cursor = 0;

    echo '<table>';

    foreach ($excel->getWorksheetIterator() as $worksheet) {
      $maxCell = $worksheet->getHighestRowAndColumn();

      for ($i = 0; $i <= $maxCell['row']; $i++) {
        $cursor++;
        /*echo '<tr>';
        echo '<td>';
        if ($worksheet->getStyle('A' . ($i + 1))->getFont()->getUnderline() === 'single')
          echo $worksheet->getCell('A' . ($i + 1));
        echo '</td>';
        echo '<td>' . $worksheet->getCell('A' . $i) . '</td>';
        echo '<td>' . $worksheet->getCell('C' . $i) . '</td>';
        echo '<td>' . $worksheet->getCell('J' . $i) . '</td>';
        echo '</tr>';*/

        if ($worksheet->getStyle('A' . ($i + 1))->getFont()->getUnderline() === 'single') {
          $sheet->setCellValue('A' . ($cursor + 1), $worksheet->getCell('A' . ($i + 1)));
          $sheet->setCellValue('B' . ($cursor + 1), ($worksheet->getCell('B' . ($i + 1)) . ' ' . ($worksheet->getCell('C' . ($i + 1)))));
        }

        if ($worksheet->getCell('J' . ($i + 1)) == '-' || $worksheet->getCell('J' . ($i + 1)) == '')
          $sheet->setCellValue('D' . ($cursor + 1), $worksheet->getCell('I' . ($i + 1)));
        else
          $sheet->setCellValue('D' . ($cursor + 1), $worksheet->getCell('J' . ($i + 1)));

        $sheet->setCellValue('C' . ($cursor + 1), $worksheet->getCell('A' . ($i + 1)));
        $sheet->setCellValue('E' . ($cursor + 1), $worksheet->getCell('C' . ($i + 1)));
        $sheet->setCellValue('F' . ($cursor + 1), $worksheet->getCell('H' . ($i + 1)));
      }


      $worksheet = $worksheet->toArray();
      $sheets = array_merge($sheets, $worksheet);
    }

    echo '</table>';

    $norm = [];

    /*foreach ($sheets as $row) {
      $arr = [];
      $arr['code'] = $row[0];
      $arr['name'] = $row[1];
      $arr['mark'] = $row[2];
      $arr['num'] = $row[6];
      $arr['ei'] = $row[7];
      $arr['send'] = $row[9];

      array_push($norm, $arr);
    }

    foreach ($norm as $key => $row) {
      $sheet->setCellValue('B' . ($key + 1), $row['code']);
      $sheet->setCellValue('C' . ($key + 1), $row['name']);
      $sheet->setCellValue('D' . ($key + 1), $row['mark']);
      $sheet->setCellValue('E' . ($key + 1), $row['ei']);
      $sheet->setCellValue('F' . ($key + 1), $row['num']);
      $sheet->setCellValue('G' . ($key + 1), $row['send']);
    }*/

    $writer->save($_SERVER['DOCUMENT_ROOT'] . '/app/Http/controllers/' . $file . '-new.xlsx');


  }
}
