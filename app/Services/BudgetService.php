<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Budget;
use App\Models\Version;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BudgetService
{

  protected $spreadsheet;
  protected $writer;

  public function __construct(Spreadsheet $spreadsheet)
  {
    $this->spreadsheet = $spreadsheet;
    $this->writer = new Xlsx($spreadsheet);
  }

  /**
   * Getting budget by period and version.
   *
   * @param $periods array
   * @param $version integer
   * @param $version_involvement integer
   * @param $regions array
   *
   * @return \Illuminate\Support\Collection
   */
  public function getBudget($periods, $version, $version_involvement, $regions = null)
  {
    $dkreSql = $regions ? implode(',', $regions) : implode(',', Dkre::get()->pluck('id')->toArray());
    $periodSql = implode(',', $periods);
    $budget = DB::select("
      SELECT budgets.activity_type_id,
      budgets.payment_balance_article_general as article,
      activity_types.name as activity,
      dkres.region,
      budgets.dkre_id,
      SUM(budgets.count) as budget,
      involvements.involve_last,
      involvements.involve_current,
      involvements.involve_turnover,
      involvements.prepayment_current,
      involvements.prepayment_next,
      ROUND(SUM(budgets.count) -
      IFNULL(involvements.involve_last, 0) -
      IFNULL(involvements.involve_current, 0) -
      IFNULL(involvements.involve_turnover, 0) +
      IFNULL(involvements.prepayment_current, 0) +
      IFNULL(involvements.prepayment_next, 0), 3) as sum
      
      FROM `budgets`
      
      LEFT JOIN
      (SELECT
       dkre_id,
       activity_type_id,
       payment_balance_article_general,
       SUM(involvements.involve_by_prepayment_last_year) as involve_last,
       SUM(involvements.involve_by_prepayment_current_year) as involve_current,
       SUM(involvements.involve_by_turnover) as involve_turnover,
       SUM(involvements.prepayment_current_year) as prepayment_current,
       SUM(involvements.prepayment_next_year) as prepayment_next FROM `involvements`
      WHERE period_id in ($periodSql) AND version_id=$version_involvement
      GROUP BY involvements.activity_type_id, involvements.payment_balance_article_general, involvements.dkre_id
      ) involvements
      ON involvements.dkre_id = budgets.dkre_id
      AND involvements.activity_type_id = budgets.activity_type_id
      AND involvements.payment_balance_article_general = budgets.payment_balance_article_general    
      
      JOIN activity_types ON budgets.activity_type_id = activity_types.id
      JOIN dkres ON budgets.dkre_id = dkres.id
      
      WHERE budgets.period_id IN ($periodSql) AND budgets.version_id=$version AND budgets.dkre_id IN ($dkreSql)
      GROUP BY budgets.activity_type_id,
      budgets.payment_balance_article_general,
      budgets.dkre_id,
      involvements.involve_last,
      involvements.involve_current,
      involvements.involve_turnover,
      involvements.prepayment_current,
      involvements.prepayment_next
      ORDER BY budgets.dkre_id, budgets.activity_type_id
    ");

    return collect($budget)->groupBy('dkre_id')->map(function ($item, $key) {
      return collect([
        'dkre' => count($item) ? $item[0]->region : '',
        'dkre_id' => count($item) ? $item[0]->dkre_id : '',
        'activity' => $item->groupBy('activity_type_id')->map(function ($item) {
//          dd($item->toArray());
//          dd($item->groupBy('article')->map(function ($item) {
//           return $item[0]->budget;
//          })->toArray());
          return collect([
            'name' => count($item) ? $item[0]->activity : '',
            'activity_id' => count($item) ? $item[0]->activity_type_id : '',
            'involve_last' => round($item->sum('involve_last'), 3),
            'involve_current' => round($item->sum('involve_current'), 3),
            'involve_turnover' => round($item->sum('involve_turnover'), 3),
            'prepayment_current' => round($item->sum('prepayment_current'), 3),
            'prepayment_next' => round($item->sum('prepayment_next'), 3),
            'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
            'finance' => round($item->sum('sum'), 3),
            'article' => $item->groupBy('article')->map(function ($item) {
              return $item[0]->budget;
            }),
          ]);
        })->values(),
        'total' => collect([
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
          'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
          'finance' => round($item->sum('sum'), 3),
          'article' => $item->groupBy('article')->map(function ($item) {
            return round($item->sum('budget'), 3);
          }),
        ])
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => collect($budget)->groupBy('activity_type_id')->map(function ($item) {
        return collect([
          'name' => count($item) ? $item[0]->activity : '',
          'activity_id' => count($item) ? $item[0]->activity_type_id : '',
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
          'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
          'finance' => round($item->sum('sum'), 3),
          'budget' => round($item->sum('budget'), 3),
          'article' => $item->groupBy('article')->map(function ($item) {
            return round($item->sum('budget'), 3);
          }),
        ]);
      })->values(),
      'total' => collect([
        'involve_last' => round(collect($budget)->sum('involve_last'), 3),
        'involve_current' => round(collect($budget)->sum('involve_current'), 3),
        'involve_turnover' => round(collect($budget)->sum('involve_turnover'), 3),
        'prepayment_current' => round(collect($budget)->sum('prepayment_current'), 3),
        'prepayment_next' => round(collect($budget)->sum('prepayment_next'), 3),
        'finance_material' => round(collect($budget)->where('article', '63400')->sum('sum'), 3),
        'finance' => round(collect($budget)->sum('sum'), 3),
        'article' => collect($budget)->groupBy('article')->map(function ($item) {
          return round($item->sum('budget'), 3);
        }),
      ])
    ]))->values();
  }

  /**
   * Получаем список ДКРЭ
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getDkres()
  {
    return Dkre::all()->unique('name')->values();
  }

  /**
   * Получаем список Регионов
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getRegions()
  {
    return Dkre::all();
  }

  /**
   * Получаем список Периодов
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getPeriods($type = null)
  {
    return !$type ? Period::all() : Period::where('type', $type)->get();
  }

  /**
   * Получаем список Версий
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getVersions()
  {
    return Version::all();
  }

  /**
   * Парсим загруженный файл
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getUploadFile($file, $version)
  {
    $excel = IOFactory::load($file);

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    return ParserInObjectExcelHelper($data, $version);
  }

  /**
   * Экспортируем бюджет
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function export($period, $version)
  {
    $periodName = Period::find($period)->name;
    $versionName = Version::find($version)->name;

    $this->spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    $this->spreadsheet->getDefaultStyle()->getFont()->setSize('14');
    $budget = $this->getBudget([$period], $version, 1);

    /* Шапка */
    $this->spreadsheet->setActiveSheetIndex(0);
    $sheet = $this->spreadsheet->getActiveSheet();
    /* 1-й столбец */
    $sheet->mergeCells('A1:A2');
    $sheet->setCellValue('A1', 'ДКРЭ/ВД');
    /* 2-й столбец */
    $sheet->mergeCells('B1:B2');
    $sheet->setCellValue('B1', 'Бюджет на новые закупаемые');
    /* 1-я объединенная строка */
    $sheet->mergeCells('C1:F1');
    $sheet->setCellValue('C1', 'Вовлечение');
    /* 3-й столбец */
    $sheet->setCellValue('C2', 'ИТОГО:');
    /* 4-й столбец */
    $sheet->setCellValue('D2', 'за счет прошлого года');
    /* 5-й столбец */
    $sheet->setCellValue('E2', 'за счет сверх-норматива');
    /* 6-й столбец */
    $sheet->setCellValue('F2', 'за счет текущего года');
    /* 2-я объединенная строка */
    $sheet->mergeCells('G1:I1');
    $sheet->setCellValue('G1', 'Опережающее финансирование');
    /* 7-й столбец */
    $sheet->setCellValue('G2', 'ИТОГО:');
    /* 8-й столбец */
    $sheet->setCellValue('H2', 'за счет текущего года');
    /* 9-й столбец */
    $sheet->setCellValue('I2', 'за счет следующего года');
    /* 10-й столбец */
    $sheet->mergeCells('J1:J2');
    $sheet->setCellValue('J1', 'Лимит на закупку материалов');
    /* 2-я объединенная строка */
    $sheet->mergeCells('K1:O1');
    $sheet->setCellValue('K1', 'Опережающее финансирование');
    /* 11-й столбец */
    $sheet->setCellValue('K2', 'ИТОГО:');
    /* 12-й столбец */
    $sheet->setCellValue('L2', 'Дизельное топливо');
    /* 13-й столбец */
    $sheet->setCellValue('M2', 'Мазут');
    /* 14-й столбец */
    $sheet->setCellValue('N2', 'Уголь');
    /* 15-й столбец */
    $sheet->setCellValue('O2', ' 	Другие виды топлива (бензин и газ)');
    /* 16 -й столбец */
    $sheet->mergeCells('P1:P2');
    $sheet->setCellValue('P1', 'ВСЕГО:');

    $sheet->getSheetView()->setZoomScale(75);
    $sheet->getStyle('A1:P74')->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('A1:P2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A1:P2')->getAlignment()->setVertical('center');

    $rowOffset = 0;

    foreach ($budget as $key => $dkre) {
      $rowIndex = $key + 3;

      $sheet->setCellValue('A' . ($rowIndex + $rowOffset), $dkre['dkre']);
      $sheet->setCellValue('B' . ($rowIndex + $rowOffset), $dkre['total']['article']['63400']);
      $sheet->setCellValue('C' . ($rowIndex + $rowOffset), $dkre['total']['involve_last'] + $dkre['total']['involve_current'] + $dkre['total']['involve_turnover']);
      $sheet->setCellValue('D' . ($rowIndex + $rowOffset), $dkre['total']['involve_last']);
      $sheet->setCellValue('E' . ($rowIndex + $rowOffset), $dkre['total']['involve_current']);
      $sheet->setCellValue('F' . ($rowIndex + $rowOffset), $dkre['total']['involve_turnover']);
      $sheet->setCellValue('G' . ($rowIndex + $rowOffset), $dkre['total']['prepayment_current'] + $dkre['total']['prepayment_next']);
      $sheet->setCellValue('H' . ($rowIndex + $rowOffset), $dkre['total']['prepayment_current']);
      $sheet->setCellValue('I' . ($rowIndex + $rowOffset), $dkre['total']['prepayment_next']);
      $sheet->setCellValue('J' . ($rowIndex + $rowOffset), $dkre['total']['finance_material']);
      $totalFuel = $dkre['total']['article']['63310'] + $dkre['total']['article']['63320'] + $dkre['total']['article']['63330'] + $dkre['total']['article']['63340'];
      $sheet->setCellValue('K' . ($rowIndex + $rowOffset), $totalFuel);
      $sheet->setCellValue('L' . ($rowIndex + $rowOffset), $dkre['total']['article']['63310']);
      $sheet->setCellValue('M' . ($rowIndex + $rowOffset), $dkre['total']['article']['63320']);
      $sheet->setCellValue('N' . ($rowIndex + $rowOffset), $dkre['total']['article']['63330']);
      $sheet->setCellValue('O' . ($rowIndex + $rowOffset), $dkre['total']['article']['63340']);
      $sheet->setCellValue('P' . ($rowIndex + $rowOffset), $dkre['total']['finance']);

      foreach ($dkre['activity'] as $key => $activity) {
        $rowOffset++;
        $sheet->setCellValue('A' . ($rowIndex + $rowOffset), $activity['name']);
        $sheet->setCellValue('B' . ($rowIndex + $rowOffset), $activity['article']['63400']);
        $sheet->setCellValue('C' . ($rowIndex + $rowOffset), $activity['involve_last'] + $dkre['total']['involve_current'] + $dkre['total']['involve_turnover']);
        $sheet->setCellValue('D' . ($rowIndex + $rowOffset), $activity['involve_last']);
        $sheet->setCellValue('E' . ($rowIndex + $rowOffset), $activity['involve_current']);
        $sheet->setCellValue('F' . ($rowIndex + $rowOffset), $activity['involve_turnover']);
        $sheet->setCellValue('G' . ($rowIndex + $rowOffset), $activity['prepayment_current'] + $activity['prepayment_next']);
        $sheet->setCellValue('H' . ($rowIndex + $rowOffset), $activity['prepayment_current']);
        $sheet->setCellValue('I' . ($rowIndex + $rowOffset), $activity['prepayment_next']);
        $sheet->setCellValue('J' . ($rowIndex + $rowOffset), $activity['finance_material']);
        $fuel_63310 = in_array('63310', $activity['article']->toArray()) ? $activity['article']['63310'] : 0;
        $fuel_63320 = in_array('63320', $activity['article']->toArray()) ? $activity['article']['63320'] : 0;
        $fuel_63330 = in_array('63330', $activity['article']->toArray()) ? $activity['article']['63330'] : 0;
        $fuel_63340 = in_array('63340', $activity['article']->toArray()) ? $activity['article']['63340'] : 0;
        $totalFuel = $fuel_63310 + $fuel_63320 + $fuel_63330 + $fuel_63340;

        $sheet->setCellValue('K' . ($rowIndex + $rowOffset), $totalFuel);
        $sheet->setCellValue('L' . ($rowIndex + $rowOffset), $fuel_63310);
        $sheet->setCellValue('M' . ($rowIndex + $rowOffset), $fuel_63320);
        $sheet->setCellValue('N' . ($rowIndex + $rowOffset), $fuel_63330);
        $sheet->setCellValue('O' . ($rowIndex + $rowOffset), $fuel_63340);
        $sheet->setCellValue('P' . ($rowIndex + $rowOffset), $activity['finance']);
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
