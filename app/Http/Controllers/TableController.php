<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableExport;
use App\Services\TableService;


class TableController extends Controller
{
  protected $tableService;

  public function __construct(TableService $tableService)
  {
    $this->tableService = $tableService;
  }

  /**
   * Export vue-table-2 table in xlsx
   *
   * @param $request
   * @return string
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function export(TableExport $request)
  {
    $data = $request->input('data');
    $headings = $request->input('headings');

    return $this->tableService->export($data, $headings);
  }
}
