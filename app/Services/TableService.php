<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TableService
{
  protected $spreadsheet;
  protected $writer;

  public function __construct(Spreadsheet $spreadsheet)
  {
    $this->spreadsheet = $spreadsheet;
    $this->writer = new Xlsx($spreadsheet);
  }

  /**
   * Export vue-table-2 table in xlsx
   *
   * @param $data array
   * @param $headings array
   * @return string
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function export($data, $headings)
  {
    $sheet = $this->spreadsheet->getActiveSheet();
    $col = 1;
    $exception = ['id', 'actions', 'started', 'temp_id'];

    foreach ($headings as $key => $value) {
      if (!in_array($key, $exception)) {
        $sheet->setCellValueByColumnAndRow($col, 1, $value);
        $col++;
      }
    }

    foreach ($data as $row => $obj) {
      $col = 1;
      foreach ($obj as $key => $value) {
        if (!in_array($key, $exception)) {

          if (is_array($value)) {
            $sheet->setCellValueByColumnAndRow($col, $row + 2, implode(';', $value));
          } else {
            $sheet->setCellValueByColumnAndRow($col, $row + 2, $value);
          }

          $col++;
        }
      }
    }

    ob_start();
    $this->writer->save('php://output');
    $content = ob_get_contents();
    ob_end_clean();

    Storage::disk('local')->put("public/table.xlsx", $content);

    return asset('storage/table.xlsx');
  }
}
