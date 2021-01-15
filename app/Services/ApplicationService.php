<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Budget;
use App\Models\PaymentBalanceArticle;
use App\Models\Dkre;
use App\Models\Period;
use App\Models\Finance;
use App\Models\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ApplicationService
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
   *
   * @return \Illuminate\Support\Collection
   */
  public function getApplications($periods, $article, $version, $version_budget, $version_involvement, $version_f22, $version_shipment)
  {
    $periodSql = $periodShipment = implode(',', $periods);

//    if (count($periods) == 1) {
//      if ($periods[0] == 3 || $periods[0] == 4 || $periods[0] == 5) {
//        $periodShipment = implode(',', [3, 4, 5]);
//      } else if ($periods[0] == 7 || $periods[0] == 8 || $periods[0] == 9) {
//        $periodShipment = implode(',', [3, 4, 5, 7, 8, 9]);
//      } else if ($periods[0] == 11 || $periods[0] == 12 || $periods[0] == 13) {
//        $periodShipment = implode(',', [3, 4, 5, 7, 8, 9, 11, 12, 13]);
//      } else if ($periods[0] == 15 || $periods[0] == 16 || $periods[0] == 17) {
//        $periodShipment = implode(',', [3, 4, 5, 7, 8, 9, 11, 12, 13, 15, 16, 17]);
//      }
//    }

    $application = DB::select("
    SELECT applications.dkre_id, 
    applications.activity_type_id, 
    applications.payment_balance_article_id,
    payment_balance_articles.name as article,
    activity_types.name as activity,
    dkres.region as region,
    applications.source_id,
    SUM(applications.count) finance,
    budgets.sum as budget,
    finances.sum as f22,
    shipments.sum as shipment
    
    FROM applications
    
    LEFT JOIN (
    SELECT budgets.activity_type_id, 
    budgets.payment_balance_article_general, 
    budgets.dkre_id, 
    ROUND(SUM(budgets.count) - 
    IFNULL(involvements.involve_last, 0) - 
    IFNULL(involvements.involve_current, 0) - 
    IFNULL(involvements.involve_turnover, 0) + 
    IFNULL(involvements.prepayment_current, 0) + 
    IFNULL(involvements.prepayment_next, 0) - 
    total_applications.sum, 3) as sum
    
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
    
    LEFT JOIN
    (SELECT 
     SUM(count) as sum,  
     payment_balance_article_general, 
     activity_type_id, 
     dkre_id FROM applications
    WHERE period_id IN ($periodSql) AND version_id=$version
    GROUP BY payment_balance_article_general, activity_type_id, dkre_id
    ) total_applications 
    ON total_applications.payment_balance_article_general = budgets.payment_balance_article_general
    AND total_applications.activity_type_id = budgets.activity_type_id
    AND total_applications.dkre_id = budgets.dkre_id
    
    WHERE budgets.period_id IN ($periodSql) AND budgets.version_id=$version_budget
    GROUP BY budgets.activity_type_id, 
    budgets.payment_balance_article_general, 
    budgets.dkre_id, 
    involvements.involve_last,
    involvements.involve_current,
    involvements.involve_turnover,
    involvements.prepayment_current,
    involvements.prepayment_next) budgets
    ON applications.payment_balance_article_general = budgets.payment_balance_article_general
    AND applications.activity_type_id = budgets.activity_type_id
    AND applications.dkre_id = budgets.dkre_id
    
    LEFT JOIN (
    SELECT finances.activity_type_id, 
    finances.payment_balance_article_sub_general, 
    finances.source_id,
    ROUND((SUM(finances.count) - 
    total_applications.sum), 3) as sum
    FROM `finances`
    
    LEFT JOIN
    (SELECT 
     SUM(count) as sum,  
     payment_balance_article_sub_general, 
     activity_type_id, 
     source_id FROM applications
    WHERE period_id IN ($periodSql) AND version_id=$version
    GROUP BY payment_balance_article_sub_general, activity_type_id, source_id
    ) total_applications     
    ON total_applications.payment_balance_article_sub_general = finances.payment_balance_article_sub_general
    AND total_applications.activity_type_id = finances.activity_type_id
    AND total_applications.source_id = finances.source_id
        
    WHERE finances.period_id IN ($periodSql) AND finances.version_id=$version_f22
    GROUP BY finances.activity_type_id, 
    finances.payment_balance_article_sub_general, 
    finances.source_id) finances
    ON applications.payment_balance_article_sub_general = finances.payment_balance_article_sub_general
    AND applications.activity_type_id = finances.activity_type_id
    AND applications.source_id = finances.source_id
    
    LEFT JOIN (
    SELECT
      applications.activity_type_id, 
      applications.payment_balance_article_id, 
      applications.source_id,
      applications.dkre_id,
      ROUND(IFNULL(total_shipments.sum - SUM(applications.count), 0), 3) as sum
      FROM applications
      
      LEFT JOIN (    
      SELECT 
      shipments.activity_type_id, 
      shipments.payment_balance_article_id, 
      shipments.source_id,
      shipments.dkre_id,
      SUM(shipments.count) as sum
      FROM `shipments`
      WHERE period_id IN ($periodShipment) AND version_id=$version_shipment
      GROUP BY shipments.payment_balance_article_id, shipments.activity_type_id, shipments.source_id, shipments.dkre_id
      ) total_shipments
      ON applications.payment_balance_article_id = total_shipments.payment_balance_article_id
      AND applications.activity_type_id = total_shipments.activity_type_id
      AND applications.source_id = total_shipments.source_id
      AND applications.dkre_id = total_shipments.dkre_id
      
      WHERE period_id IN ($periodSql) AND version_id=$version
      GROUP BY applications.payment_balance_article_id, applications.activity_type_id, applications.source_id, applications.dkre_id
      ) shipments
    ON applications.payment_balance_article_id = shipments.payment_balance_article_id
    AND applications.activity_type_id = shipments.activity_type_id
    AND applications.source_id = shipments.source_id
    AND applications.dkre_id = shipments.dkre_id
    
    JOIN payment_balance_articles
    ON applications.payment_balance_article_id = payment_balance_articles.id
    JOIN activity_types
    ON applications.activity_type_id = activity_types.id
    JOIN dkres
    ON applications.dkre_id = dkres.id
    
    WHERE applications.period_id in ($periodSql) AND applications.version_id=$version AND applications.payment_balance_article_id=$article
    GROUP BY applications.dkre_id, 
    applications.payment_balance_article_id, 
    applications.activity_type_id, 
    applications.source_id,
    budgets.sum,
    finances.sum,
    shipments.sum
    ORDER BY applications.dkre_id
    ");

    $application = collect($application)->groupBy('dkre_id')->map(function ($item) {
      return collect([
        'dkre' => count($item) ? $item[0]->region : '',
        'dkre_id' => count($item) ? $item[0]->dkre_id : '',
        'activity' => $item->groupBy('activity_type_id')->map(function ($item) {
          return collect([
            'name' => count($item) ? $item[0]->activity : '',
            'activity_id' => count($item) ? $item[0]->activity_type_id : '',
            'source' => $item->groupBy('source_id')->map(function ($item, $key) {
              return $item[0];
            })
          ]);
        })->values(),
        'total' => $item->groupBy('source_id')->map(function ($item, $key) {
          return collect([
            'f22' => round($item->sum('f22'), 3),
            'budget' => round($item->sum('budget'), 3),
            'finance' => round($item->sum('finance'), 3),
            'shipment' => round($item->sum('shipment'), 3),
          ]);
        })
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => collect($application)->groupBy('activity_type_id')->map(function ($item) {
        return collect([
          'name' => count($item) ? $item[0]->activity : '',
          'activity_id' => count($item) ? $item[0]->activity_type_id : '',
          'source' => $item->groupBy('source_id')->map(function ($item, $key) {
            return collect([
              'f22' => round($item[0]->f22, 3),
              'finance' => round($item->sum('finance'), 3),
              'budget' => round($item->sum('budget'), 3),
              'shipment' => round($item->sum('shipment'), 3),
            ]);
          })
        ]);
      })->values(),
      'total' => collect($application)->groupBy('source_id')->map(function ($item, $key) {
        return collect([
          'f22' => round($item[0]->f22, 3),
          'budget' => round($item->sum('budget'), 3),
          'finance' => round($item->sum('finance'), 3),
          'shipment' => round($item->sum('shipment'), 3),
        ]);
      })
    ]))->values();

    return $application;

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
   * Получаем список статей
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getArticles()
  {
    return PaymentBalanceArticle::whereNotBetween('code', [63411, 63418])
      ->whereNotBetween('code', [63422, 63426])->get();
  }

  /**
   * Парсим загруженный файл
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getUploadFile($file, $period)
  {
    $excel = IOFactory::load($file);

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    return ParserInObjectExcelHelper($data, null, $period);
  }

  /**
   * Консолидируем квартал
   *
   * @param $period
   * @return \Illuminate\Support\Collection
   */
  public function consolidatePeriod($period)
  {
    if ($period == 2) {
      $months = '3, 4, 5';
    } else if ($period == 6) {
      $months = '7, 8, 9';
    } else if ($period == 10) {
      $months = '11, 12, 13';
    } else if ($period == 14) {
      $months = '15, 16, 17';
    } else if ($period == 2) {
      $months = '3, 4, 5, 7, 8, 9, 11, 12, 13, 15, 16, 17';
    }

    $data = DB::select("
      SELECT payment_balance_article_id, activity_type_id, dkre_id, source_id, version_id, payment_balance_article_general, payment_balance_article_sub_general, SUM(count) as count
      FROM `applications` 
      WHERE period_id in ($months) AND version_id = 1
      GROUP BY payment_balance_article_id, activity_type_id, dkre_id, source_id, version_id, payment_balance_article_general, payment_balance_article_sub_general
    ");

    return collect($data)->map(function ($item) use ($period) {
      return collect($item)->put('period_id', $period);
    })->toArray();
  }

  /**
   * Экспортируем
   *
   * @param $periods
   * @return \Illuminate\Support\Collection
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function exportPeriod($period)
  {
    $period_name = Period::find($period)->name;
    $this->spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    $this->spreadsheet->getDefaultStyle()->getFont()->setSize('14');
    $application = Application::where('period_id', $period)->with(['article', 'dkre', 'period'])->get();

    $application->groupBy('dkre_id')->each(function ($item, $key) {
      if ($key == 1) {
        $this->spreadsheet->setActiveSheetIndex(0);
      } else {
        $this->spreadsheet->createSheet();
        $this->spreadsheet->setActiveSheetIndex($key - 1);
      }

      $sheet = $this->spreadsheet->getActiveSheet();
      $sheet->getSheetView()->setZoomScale(75);
      $sheet->setTitle(str_replace('ДКРЭ_', '', $item[0]->dkre->area));
      $sheet->getStyle('A5:M43')->getBorders()->getAllBorders()->setBorderStyle('thin');
      $sheet->getStyle('C8:M43')->getNumberFormat()->setFormatCode('# ##0.000_-;#;-#;"---"');
      $sheet->getStyle('C8:M43')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C8:M43')->getAlignment()->setVertical('center');
      /* Заголовок */
      $sheet->setCellValue('A1', 'ЗАЯВКА на финансирование поставки МТР на ' . $item[0]->period->name . ' ' . date('Y') . ' года');
      $sheet->getStyle('A1')->getFont()->setBold(true);
      $sheet->getStyle('A1')->getFont()->setSize('20');
      $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
      $sheet->mergeCells('A1:M1');
      /* ДКРЭ */
      $sheet->setCellValue('B3', 'Наименование филиала');
      $sheet->setCellValue('C3', 'ДКРЭ (' . $item[0]->dkre->area . ')');
      $sheet->getStyle('C3')->getAlignment()->setHorizontal('center');
      $sheet->mergeCells('C3:F3');
      /* ЕИ */
      $sheet->setCellValue('A4', "(тыс.руб. с НДС)");
      $sheet->getStyle('A4')->getFont()->setSize('12');
      $sheet->getStyle('A4')->getAlignment()->setHorizontal('right');
      $sheet->mergeCells('A4:M4');
      /* Заголовки */
      $sheet->setCellValue('A5', '№ статьи');
      $sheet->mergeCells('A5:A7');
      $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('A5')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('A')->setAutoSize(false);
      $sheet->getColumnDimension('A')->setWidth(9);
      /* Статьи */
      $sheet->setCellValue('B5', 'Наименование статей');
      $sheet->mergeCells('B5:B7');
      $sheet->getStyle('B5')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('B5')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('B')->setAutoSize(false);
      $sheet->getColumnDimension('B')->setWidth(44);
      /* Период */
      $sheet->setCellValue('C5', $item[0]->period->name . ' ' . date('Y') . ' г . ');
      $sheet->mergeCells('C5:M5');
      $sheet->getStyle('C5')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C5')->getAlignment()->setVertical('center');
      /* ЦЗ */
      $sheet->setCellValue('C6', 'Централизованная поставка');
      $sheet->mergeCells('C6:F6');
      $sheet->getStyle('C6')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C6')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('C')->setAutoSize(false);
      $sheet->getColumnDimension('C')->setWidth(14);
      /* Итого */
      $sheet->setCellValue('G6', 'Итого');
      $sheet->mergeCells('G6:G7');
      $sheet->getStyle('G6')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('G6')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('G')->setAutoSize(false);
      $sheet->getColumnDimension('G')->setWidth(14);
      /* ПЕР */
      $sheet->setCellValue('C7', 'Перевозки');
      $sheet->getStyle('C7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('C')->setAutoSize(false);
      $sheet->getColumnDimension('C')->setWidth(14);
      /* ПВД */
      $sheet->setCellValue('D7', 'ПВД');
      $sheet->getStyle('D7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('D7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('D')->setAutoSize(false);
      $sheet->getColumnDimension('D')->setWidth(14);
      /* ИНВ */
      $sheet->setCellValue('E7', 'КВ');
      $sheet->getStyle('E7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('E7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('E')->setAutoSize(false);
      $sheet->getColumnDimension('E')->setWidth(14);
      /* ПРО */
      $sheet->setCellValue('F7', 'Прочие');
      $sheet->getStyle('F7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('F7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('F')->setAutoSize(false);
      $sheet->getColumnDimension('F')->setWidth(14);
      /* СЗ */
      $sheet->setCellValue('H6', 'Самостоятельная закупка');
      $sheet->mergeCells('H6:K6');
      $sheet->getStyle('H6')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('H6')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('H')->setAutoSize(false);
      $sheet->getColumnDimension('H')->setWidth(14);
      /* Итого */
      $sheet->setCellValue('L6', 'Итого');
      $sheet->mergeCells('L6:L7');
      $sheet->getStyle('L6')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('L6')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('L')->setAutoSize(false);
      $sheet->getColumnDimension('L')->setWidth(14);
      /* ПЕР */
      $sheet->setCellValue('H7', 'Перевозки');
      $sheet->getStyle('H7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('H7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('H')->setAutoSize(false);
      $sheet->getColumnDimension('H')->setWidth(14);
      /* ПВД */
      $sheet->setCellValue('I7', 'ПВД');
      $sheet->getStyle('I7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('I7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('I')->setAutoSize(false);
      $sheet->getColumnDimension('I')->setWidth(14);
      /* ИНВ */
      $sheet->setCellValue('J7', 'КВ');
      $sheet->getStyle('J7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('J7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('J')->setAutoSize(false);
      $sheet->getColumnDimension('J')->setWidth(14);
      /* ПРО */
      $sheet->setCellValue('K7', 'Самостоятельная закупка');
      $sheet->getStyle('K7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('K7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('K')->setAutoSize(false);
      $sheet->getColumnDimension('K')->setWidth(14);
      /* ВСЕГО */
      $sheet->setCellValue('M6', 'ВСЕГО');
      $sheet->mergeCells('M6:M7');
      $sheet->getStyle('M6')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('M6')->getAlignment()->setVertical('center');
      $sheet->getStyle('M6')->getFont()->setBold(true);
      $sheet->getColumnDimension('M')->setAutoSize(false);
      $sheet->getColumnDimension('M')->setWidth(14);
//      /* ЧДФ */
//      $sheet->setCellValue('N8', 'Закупка через другие филиалы');
//      $sheet->mergeCells('N8:Q8');
//      $sheet->getStyle('N8')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('N8')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('N')->setAutoSize(false);
//      $sheet->getColumnDimension('N')->setWidth(14);
//      /* Итого */
//      $sheet->setCellValue('R8', 'Итого');
//      $sheet->mergeCells('R8:R9');
//      $sheet->getStyle('R8')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('R8')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('R')->setAutoSize(false);
//      $sheet->getColumnDimension('R')->setWidth(14);
//      /* ПЕР */
//      $sheet->setCellValue('N9', 'Перевозки');
//      $sheet->getStyle('N9')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('N9')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('N')->setAutoSize(false);
//      $sheet->getColumnDimension('N')->setWidth(14);
//      /* ПВД */
//      $sheet->setCellValue('O9', 'ПВД');
//      $sheet->getStyle('O9')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('O9')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('O')->setAutoSize(false);
//      $sheet->getColumnDimension('O')->setWidth(14);
//      /* ИНВ */
//      $sheet->setCellValue('P9', 'КВ');
//      $sheet->getStyle('P9')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('P9')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('P')->setAutoSize(false);
//      $sheet->getColumnDimension('P')->setWidth(14);
//      /* ПРО */
//      $sheet->setCellValue('Q9', 'Прочие');
//      $sheet->getStyle('Q9')->getAlignment()->setHorizontal('center');
//      $sheet->getStyle('Q9')->getAlignment()->setVertical('center');
//      $sheet->getColumnDimension('Q')->setAutoSize(false);
//      $sheet->getColumnDimension('Q')->setWidth(14);

      /* Таблица с данными*/
      $sheet->setCellValue('A8', '');
      $sheet->setCellValue('B8', 'ВСЕГО РЖДС, в том числе:');
      $sheet->setCellValue('C8', '=C9 + C14');
      $sheet->setCellValue('D8', '=D9 + D14');
      $sheet->setCellValue('E8', '=E9 + E14');
      $sheet->setCellValue('F8', '=F9 + F14');
      $sheet->setCellValue('G8', '=G9 + G14');
      $sheet->setCellValue('H8', '=H9 + H14');
      $sheet->setCellValue('I8', '=I9 + I14');
      $sheet->setCellValue('J8', '=J9 + J14');
      $sheet->setCellValue('K8', '=K9 + K14');
      $sheet->setCellValue('L8', '=L9 + L14');
      $sheet->setCellValue('M8', '=M9 + M14');
//      $sheet->setCellValue('N8', '=N9 + N14');
//      $sheet->setCellValue('O8', '=O9 + O14');
//      $sheet->setCellValue('P8', '=P9 + P14');
//      $sheet->setCellValue('Q8', '=Q9 + Q14');
//      $sheet->setCellValue('R8', '=R9 + R14');

      $sheet->setCellValue('A9', '63300');
      $sheet->setCellValue('B9', 'ТОПЛИВО ВСЕГО');
      $sheet->setCellValue('C9', '=SUM(C10:C13)');
      $sheet->setCellValue('D9', '=SUM(D10:D13)');
      $sheet->setCellValue('E9', '=SUM(E10:E13)');
      $sheet->setCellValue('F9', '=SUM(F10:F13)');
      $sheet->setCellValue('G9', '=SUM(G10:G13)');
      $sheet->setCellValue('H9', '=SUM(H10:H13)');
      $sheet->setCellValue('I9', '=SUM(I10:I13)');
      $sheet->setCellValue('J9', '=SUM(J10:J13)');
      $sheet->setCellValue('K9', '=SUM(K10:K13)');
      $sheet->setCellValue('L9', '=SUM(L10:L13)');
      $sheet->setCellValue('M9', '=SUM(M10:M13)');
//      $sheet->setCellValue('N9', '=SUM(N10:N13)');
//      $sheet->setCellValue('O9', '=SUM(O10:O13)');
//      $sheet->setCellValue('P9', '=SUM(P10:P13)');
//      $sheet->setCellValue('Q9', '=SUM(Q10:Q13)');
//      $sheet->setCellValue('R9', '=SUM(R10:R13)');

      $sheet->setCellValue('A14', '63400');
      $sheet->setCellValue('B14', 'МАТЕРИАЛЫ ВСЕГО');
      $sheet->setCellValue('C14', '=C15 + C24 + C36 + SUM(C31:C35)');
      $sheet->setCellValue('D14', '=D15 + D24 + D36 + SUM(D31:D35)');
      $sheet->setCellValue('E14', '=E15 + E24 + E36 + SUM(E31:E35)');
      $sheet->setCellValue('F14', '=F15 + F24 + F36 + SUM(F31:F35)');
      $sheet->setCellValue('G14', '=G15 + G24 + G36 + SUM(G31:G35)');
      $sheet->setCellValue('H14', '=H15 + H24 + H36 + SUM(H31:H35)');
      $sheet->setCellValue('I14', '=I15 + I24 + I36 + SUM(I31:I35)');
      $sheet->setCellValue('J14', '=J15 + J24 + J36 + SUM(J31:J35)');
      $sheet->setCellValue('K14', '=K15 + K24 + K36 + SUM(K31:K35)');
      $sheet->setCellValue('L14', '=L15 + L24 + L36 + SUM(L31:L35)');
      $sheet->setCellValue('M14', '=M15 + M24 + M36 + SUM(M31:M35)');
//      $sheet->setCellValue('N14', '=N15 + N24 + N36 + SUM(N31:N35)');
//      $sheet->setCellValue('O14', '=O15 + O24 + O36 + SUM(O31:O35)');
//      $sheet->setCellValue('P14', '=P15 + P24 + P36 + SUM(P31:P35)');
//      $sheet->setCellValue('Q14', '=Q15 + Q24 + Q36 + SUM(Q31:Q35)');
//      $sheet->setCellValue('R14', '=R15 + R24 + R36 + SUM(R31:R35)');

      $sheet->setCellValue('A15', '63410');
      $sheet->setCellValue('B15', 'материалы верхнего строения пути в т . ч .:');
      $sheet->setCellValue('C15', '=SUM(C16:C23)');
      $sheet->setCellValue('D15', '=SUM(D16:D23)');
      $sheet->setCellValue('E15', '=SUM(E16:E23)');
      $sheet->setCellValue('F15', '=SUM(F16:F23)');
      $sheet->setCellValue('G15', '=SUM(G16:G23)');
      $sheet->setCellValue('H15', '=SUM(H16:H23)');
      $sheet->setCellValue('I15', '=SUM(I16:I23)');
      $sheet->setCellValue('J15', '=SUM(J16:J23)');
      $sheet->setCellValue('K15', '=SUM(K16:K23)');
      $sheet->setCellValue('L15', '=SUM(L16:L23)');
      $sheet->setCellValue('M15', '=SUM(M16:M23)');
//      $sheet->setCellValue('N15', '=SUM(N16:N23)');
//      $sheet->setCellValue('O15', '=SUM(O16:O23)');
//      $sheet->setCellValue('P15', '=SUM(P16:P23)');
//      $sheet->setCellValue('Q15', '=SUM(Q16:Q23)');
//      $sheet->setCellValue('R15', '=SUM(R16:R23)');

      $sheet->setCellValue('A24', '63420');
      $sheet->setCellValue('B24', 'запасные части, узлы и литые детали подвижного состава в т . ч .:');
      $sheet->setCellValue('C24', '=SUM(C25:C30)');
      $sheet->setCellValue('D24', '=SUM(D25:D30)');
      $sheet->setCellValue('E24', '=SUM(E25:E30)');
      $sheet->setCellValue('F24', '=SUM(F25:F30)');
      $sheet->setCellValue('G24', '=SUM(G25:G30)');
      $sheet->setCellValue('H24', '=SUM(H25:H30)');
      $sheet->setCellValue('I24', '=SUM(I25:I30)');
      $sheet->setCellValue('J24', '=SUM(J25:J30)');
      $sheet->setCellValue('K24', '=SUM(K25:K30)');
      $sheet->setCellValue('L24', '=SUM(L25:L30)');
      $sheet->setCellValue('M24', '=SUM(M25:M30)');
//      $sheet->setCellValue('N24', '=SUM(N25:N30)');
//      $sheet->setCellValue('O24', '=SUM(O25:O30)');
//      $sheet->setCellValue('P24', '=SUM(P25:P30)');
//      $sheet->setCellValue('Q24', '=SUM(Q25:Q30)');
//      $sheet->setCellValue('R24', '=SUM(R25:R30)');

      $sheet->setCellValue('A36', '63490');
      $sheet->setCellValue('B36', 'прочие материалы, в т . ч .:');
      $sheet->setCellValue('C36', '=SUM(C37:C43)');
      $sheet->setCellValue('D36', '=SUM(D37:D43)');
      $sheet->setCellValue('E36', '=SUM(E37:E43)');
      $sheet->setCellValue('F36', '=SUM(F37:F43)');
      $sheet->setCellValue('G36', '=SUM(G37:G43)');
      $sheet->setCellValue('H36', '=SUM(H37:H43)');
      $sheet->setCellValue('I36', '=SUM(I37:I43)');
      $sheet->setCellValue('J36', '=SUM(J37:J43)');
      $sheet->setCellValue('K36', '=SUM(K37:K43)');
      $sheet->setCellValue('L36', '=SUM(L37:L43)');
      $sheet->setCellValue('M36', '=SUM(M37:M43)');
//      $sheet->setCellValue('N36', '=SUM(N37:N43)');
//      $sheet->setCellValue('O36', '=SUM(O37:O43)');
//      $sheet->setCellValue('P36', '=SUM(P37:P43)');
//      $sheet->setCellValue('Q36', '=SUM(Q37:Q43)');
//      $sheet->setCellValue('R36', '=SUM(R37:R43)');

      $item->groupBy('payment_balance_article_id')->each(function ($item, $key) use ($sheet) {
        $source = $item->groupBy('source_id')->map(function ($item) {
          $item = $item->groupBy('activity_type_id');
          $array = $item->toArray();

          return collect([
            '01' => array_key_exists('1', $array) ? round($item['1'][0]->count * 1.2 * 1000, 3) : 0,
            '21' => array_key_exists('2', $array) ? round($item['2'][0]->count * 1.2 * 1000, 3) : 0,
            '61' => array_key_exists('3', $array) ? round($item['3'][0]->count * 1.2 * 1000, 3) : 0,
            '81' => array_key_exists('4', $array) ? round($item['4'][0]->count * 1.2 * 1000, 3) : 0,
          ]);
        });

        if ($key <= 4) {
          $sheet->setCellValue('A' . (9 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (9 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (9 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (9 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (9 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (9 + $key), $source['1']['81']);
          $sheet->setCellValue('G' . (9 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81']);
          $sheet->setCellValue('H' . (9 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (9 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (9 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (9 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (9 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (9 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (9 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (9 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (9 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (9 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (9 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key <= 12) {
          $sheet->setCellValue('A' . (11 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (11 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (11 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (11 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (11 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (11 + $key), $source['1']['81']);
          $sheet->setCellValue('H' . (11 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (11 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (11 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (11 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (11 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (11 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (11 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (11 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (11 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (11 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key <= 23) {
          $sheet->setCellValue('A' . (12 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (12 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (12 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (12 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (12 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (12 + $key), $source['1']['81']);
          $sheet->setCellValue('H' . (12 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (12 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (12 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (12 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (12 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (12 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (12 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (12 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (12 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (12 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (12 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key > 23) {
          $sheet->setCellValue('A' . (13 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (13 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (13 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (13 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (13 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (13 + $key), $source['1']['81']);
          $sheet->setCellValue('H' . (13 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (13 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (13 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (13 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (13 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (13 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (13 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (13 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (13 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (13 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (13 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        }
      });
    });

    /* Формируем сводный лист */
    $length = $application->groupBy('dkre_id')->count();
    $this->spreadsheet->createSheet($length);
    $this->spreadsheet->setActiveSheetIndex($length);
    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->getSheetView()->setZoomScale(75);
    $sheet->setTitle(str_replace('ДКРЭ_', '', 'ИТОГО'));
    $sheet->getStyle('A5:M43')->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('C8:M43')->getNumberFormat()->setFormatCode('# ##0.000_-;#;-#;"---"');
    $sheet->getStyle('C8:M43')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C8:M43')->getAlignment()->setVertical('center');
    /* Заголовок */
    $sheet->setCellValue('A1', 'ЗАЯВКА на финансирование поставки МТР на ' . $period_name . ' ' . date('Y') . ' года');
    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->getStyle('A1')->getFont()->setSize('20');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('A1:M1');
    /* ДКРЭ */
    $sheet->setCellValue('B3', 'Наименование филиала');
    $sheet->setCellValue('C3', 'ДКРЭ (ИТОГ)');
    $sheet->getStyle('C3')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('C3:F3');
    /* ЕИ */
    $sheet->setCellValue('A4', "(тыс.руб. с НДС)");
    $sheet->getStyle('A4')->getFont()->setSize('12');
    $sheet->getStyle('A4')->getAlignment()->setHorizontal('right');
    $sheet->mergeCells('A4:M4');
    /* Заголовки */
    $sheet->setCellValue('A5', '№ статьи');
    $sheet->mergeCells('A5:A7');
    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A5')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('A')->setAutoSize(false);
    $sheet->getColumnDimension('A')->setWidth(9);
    /* Статьи */
    $sheet->setCellValue('B5', 'Наименование статей');
    $sheet->mergeCells('B5:B7');
    $sheet->getStyle('B5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B5')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('B')->setAutoSize(false);
    $sheet->getColumnDimension('B')->setWidth(44);
    /* Период */
    $sheet->setCellValue('C5', $period_name . ' ' . date('Y') . ' г . ');
    $sheet->mergeCells('C5:M5');
    $sheet->getStyle('C5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C5')->getAlignment()->setVertical('center');
    /* ЦЗ */
    $sheet->setCellValue('C6', 'Централизованная поставка');
    $sheet->mergeCells('C6:F6');
    $sheet->getStyle('C6')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C6')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('C')->setAutoSize(false);
    $sheet->getColumnDimension('C')->setWidth(14);
    /* Итого */
    $sheet->setCellValue('G6', 'Итого');
    $sheet->mergeCells('G6:G7');
    $sheet->getStyle('G6')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('G6')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('G')->setAutoSize(false);
    $sheet->getColumnDimension('G')->setWidth(14);
    /* ПЕР */
    $sheet->setCellValue('C7', 'Перевозки');
    $sheet->getStyle('C7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('C')->setAutoSize(false);
    $sheet->getColumnDimension('C')->setWidth(14);
    /* ПВД */
    $sheet->setCellValue('D7', 'ПВД');
    $sheet->getStyle('D7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('D7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('D')->setAutoSize(false);
    $sheet->getColumnDimension('D')->setWidth(14);
    /* ИНВ */
    $sheet->setCellValue('E7', 'КВ');
    $sheet->getStyle('E7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('E7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('E')->setAutoSize(false);
    $sheet->getColumnDimension('E')->setWidth(14);
    /* ПРО */
    $sheet->setCellValue('F7', 'Прочие');
    $sheet->getStyle('F7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('F7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('F')->setAutoSize(false);
    $sheet->getColumnDimension('F')->setWidth(14);
    /* СЗ */
    $sheet->setCellValue('H6', 'Самостоятельная закупка');
    $sheet->mergeCells('H6:K6');
    $sheet->getStyle('H6')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('H6')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('H')->setAutoSize(false);
    $sheet->getColumnDimension('H')->setWidth(14);
    /* Итого */
    $sheet->setCellValue('L6', 'Итого');
    $sheet->mergeCells('L6:L7');
    $sheet->getStyle('L6')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('L6')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('L')->setAutoSize(false);
    $sheet->getColumnDimension('L')->setWidth(14);
    /* ПЕР */
    $sheet->setCellValue('H7', 'Перевозки');
    $sheet->getStyle('H7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('H7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('H')->setAutoSize(false);
    $sheet->getColumnDimension('H')->setWidth(14);
    /* ПВД */
    $sheet->setCellValue('I7', 'ПВД');
    $sheet->getStyle('I7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('I7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('I')->setAutoSize(false);
    $sheet->getColumnDimension('I')->setWidth(14);
    /* ИНВ */
    $sheet->setCellValue('J7', 'КВ');
    $sheet->getStyle('J7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('J7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('J')->setAutoSize(false);
    $sheet->getColumnDimension('J')->setWidth(14);
    /* ПРО */
    $sheet->setCellValue('K7', 'Самостоятельная закупка');
    $sheet->getStyle('K7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('K')->setAutoSize(false);
    $sheet->getColumnDimension('K')->setWidth(14);
    /* ВСЕГО */
    $sheet->setCellValue('M6', 'ВСЕГО');
    $sheet->mergeCells('M6:M7');
    $sheet->getStyle('M6')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('M6')->getAlignment()->setVertical('center');
    $sheet->getStyle('M6')->getFont()->setBold(true);
    $sheet->getColumnDimension('M')->setAutoSize(false);
    $sheet->getColumnDimension('M')->setWidth(14);
    /* ЧДФ */
//    $sheet->setCellValue('N8', 'Закупка через другие филиалы');
//    $sheet->mergeCells('N8:Q8');
//    $sheet->getStyle('N8')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('N8')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('N')->setAutoSize(false);
//    $sheet->getColumnDimension('N')->setWidth(14);
//    /* Итого */
//    $sheet->setCellValue('R8', 'Итого');
//    $sheet->mergeCells('R8:R9');
//    $sheet->getStyle('R8')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('R8')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('R')->setAutoSize(false);
//    $sheet->getColumnDimension('R')->setWidth(14);
//    /* ПЕР */
//    $sheet->setCellValue('N9', 'Перевозки');
//    $sheet->getStyle('N9')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('N9')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('N')->setAutoSize(false);
//    $sheet->getColumnDimension('N')->setWidth(14);
//    /* ПВД */
//    $sheet->setCellValue('O9', 'ПВД');
//    $sheet->getStyle('O9')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('O9')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('O')->setAutoSize(false);
//    $sheet->getColumnDimension('O')->setWidth(14);
//    /* ИНВ */
//    $sheet->setCellValue('P9', 'КВ');
//    $sheet->getStyle('P9')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('P9')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('P')->setAutoSize(false);
//    $sheet->getColumnDimension('P')->setWidth(14);
//    /* ПРО */
//    $sheet->setCellValue('Q9', 'Прочие');
//    $sheet->getStyle('Q9')->getAlignment()->setHorizontal('center');
//    $sheet->getStyle('Q9')->getAlignment()->setVertical('center');
//    $sheet->getColumnDimension('Q')->setAutoSize(false);
//    $sheet->getColumnDimension('Q')->setWidth(14);

    /* Таблица с данными*/
    $sheet->setCellValue('A8', '');
    $sheet->setCellValue('B8', 'ВСЕГО РЖДС, в том числе:');
    $sheet->setCellValue('C8', '=C9 + C14');
    $sheet->setCellValue('D8', '=D9 + D14');
    $sheet->setCellValue('E8', '=E9 + E14');
    $sheet->setCellValue('F8', '=F9 + F14');
    $sheet->setCellValue('G8', '=G9 + G14');
    $sheet->setCellValue('H8', '=H9 + H14');
    $sheet->setCellValue('I8', '=I9 + I14');
    $sheet->setCellValue('J8', '=J9 + J14');
    $sheet->setCellValue('K8', '=K9 + K14');
    $sheet->setCellValue('L8', '=L9 + L14');
    $sheet->setCellValue('M8', '=M9 + M14');
//      $sheet->setCellValue('N8', '=N9 + N14');
//      $sheet->setCellValue('O8', '=O9 + O14');
//      $sheet->setCellValue('P8', '=P9 + P14');
//      $sheet->setCellValue('Q8', '=Q9 + Q14');
//      $sheet->setCellValue('R8', '=R9 + R14');

    $sheet->setCellValue('A9', '63300');
    $sheet->setCellValue('B9', 'ТОПЛИВО ВСЕГО');
    $sheet->setCellValue('C9', '=SUM(C10:C13)');
    $sheet->setCellValue('D9', '=SUM(D10:D13)');
    $sheet->setCellValue('E9', '=SUM(E10:E13)');
    $sheet->setCellValue('F9', '=SUM(F10:F13)');
    $sheet->setCellValue('G9', '=SUM(G10:G13)');
    $sheet->setCellValue('H9', '=SUM(H10:H13)');
    $sheet->setCellValue('I9', '=SUM(I10:I13)');
    $sheet->setCellValue('J9', '=SUM(J10:J13)');
    $sheet->setCellValue('K9', '=SUM(K10:K13)');
    $sheet->setCellValue('L9', '=SUM(L10:L13)');
    $sheet->setCellValue('M9', '=SUM(M10:M13)');
//      $sheet->setCellValue('N9', '=SUM(N10:N13)');
//      $sheet->setCellValue('O9', '=SUM(O10:O13)');
//      $sheet->setCellValue('P9', '=SUM(P10:P13)');
//      $sheet->setCellValue('Q9', '=SUM(Q10:Q13)');
//      $sheet->setCellValue('R9', '=SUM(R10:R13)');

    $sheet->setCellValue('A14', '63400');
    $sheet->setCellValue('B14', 'МАТЕРИАЛЫ ВСЕГО');
    $sheet->setCellValue('C14', '=C15 + C24 + C36 + SUM(C31:C35)');
    $sheet->setCellValue('D14', '=D15 + D24 + D36 + SUM(D31:D35)');
    $sheet->setCellValue('E14', '=E15 + E24 + E36 + SUM(E31:E35)');
    $sheet->setCellValue('F14', '=F15 + F24 + F36 + SUM(F31:F35)');
    $sheet->setCellValue('G14', '=G15 + G24 + G36 + SUM(G31:G35)');
    $sheet->setCellValue('H14', '=H15 + H24 + H36 + SUM(H31:H35)');
    $sheet->setCellValue('I14', '=I15 + I24 + I36 + SUM(I31:I35)');
    $sheet->setCellValue('J14', '=J15 + J24 + J36 + SUM(J31:J35)');
    $sheet->setCellValue('K14', '=K15 + K24 + K36 + SUM(K31:K35)');
    $sheet->setCellValue('L14', '=L15 + L24 + L36 + SUM(L31:L35)');
    $sheet->setCellValue('M14', '=M15 + M24 + M36 + SUM(M31:M35)');
//      $sheet->setCellValue('N14', '=N15 + N24 + N36 + SUM(N31:N35)');
//      $sheet->setCellValue('O14', '=O15 + O24 + O36 + SUM(O31:O35)');
//      $sheet->setCellValue('P14', '=P15 + P24 + P36 + SUM(P31:P35)');
//      $sheet->setCellValue('Q14', '=Q15 + Q24 + Q36 + SUM(Q31:Q35)');
//      $sheet->setCellValue('R14', '=R15 + R24 + R36 + SUM(R31:R35)');

    $sheet->setCellValue('A15', '63410');
    $sheet->setCellValue('B15', 'материалы верхнего строения пути в т . ч .:');
    $sheet->setCellValue('C15', '=SUM(C16:C23)');
    $sheet->setCellValue('D15', '=SUM(D16:D23)');
    $sheet->setCellValue('E15', '=SUM(E16:E23)');
    $sheet->setCellValue('F15', '=SUM(F16:F23)');
    $sheet->setCellValue('G15', '=SUM(G16:G23)');
    $sheet->setCellValue('H15', '=SUM(H16:H23)');
    $sheet->setCellValue('I15', '=SUM(I16:I23)');
    $sheet->setCellValue('J15', '=SUM(J16:J23)');
    $sheet->setCellValue('K15', '=SUM(K16:K23)');
    $sheet->setCellValue('L15', '=SUM(L16:L23)');
    $sheet->setCellValue('M15', '=SUM(M16:M23)');
//      $sheet->setCellValue('N15', '=SUM(N16:N23)');
//      $sheet->setCellValue('O15', '=SUM(O16:O23)');
//      $sheet->setCellValue('P15', '=SUM(P16:P23)');
//      $sheet->setCellValue('Q15', '=SUM(Q16:Q23)');
//      $sheet->setCellValue('R15', '=SUM(R16:R23)');

    $sheet->setCellValue('A24', '63420');
    $sheet->setCellValue('B24', 'запасные части, узлы и литые детали подвижного состава в т . ч .:');
    $sheet->setCellValue('C24', '=SUM(C25:C30)');
    $sheet->setCellValue('D24', '=SUM(D25:D30)');
    $sheet->setCellValue('E24', '=SUM(E25:E30)');
    $sheet->setCellValue('F24', '=SUM(F25:F30)');
    $sheet->setCellValue('G24', '=SUM(G25:G30)');
    $sheet->setCellValue('H24', '=SUM(H25:H30)');
    $sheet->setCellValue('I24', '=SUM(I25:I30)');
    $sheet->setCellValue('J24', '=SUM(J25:J30)');
    $sheet->setCellValue('K24', '=SUM(K25:K30)');
    $sheet->setCellValue('L24', '=SUM(L25:L30)');
    $sheet->setCellValue('M24', '=SUM(M25:M30)');
//      $sheet->setCellValue('N24', '=SUM(N25:N30)');
//      $sheet->setCellValue('O24', '=SUM(O25:O30)');
//      $sheet->setCellValue('P24', '=SUM(P25:P30)');
//      $sheet->setCellValue('Q24', '=SUM(Q25:Q30)');
//      $sheet->setCellValue('R24', '=SUM(R25:R30)');

    $sheet->setCellValue('A36', '63490');
    $sheet->setCellValue('B36', 'прочие материалы, в т . ч .:');
    $sheet->setCellValue('C36', '=SUM(C37:C43)');
    $sheet->setCellValue('D36', '=SUM(D37:D43)');
    $sheet->setCellValue('E36', '=SUM(E37:E43)');
    $sheet->setCellValue('F36', '=SUM(F37:F43)');
    $sheet->setCellValue('G36', '=SUM(G37:G43)');
    $sheet->setCellValue('H36', '=SUM(H37:H43)');
    $sheet->setCellValue('I36', '=SUM(I37:I43)');
    $sheet->setCellValue('J36', '=SUM(J37:J43)');
    $sheet->setCellValue('K36', '=SUM(K37:K43)');
    $sheet->setCellValue('L36', '=SUM(L37:L43)');
    $sheet->setCellValue('M36', '=SUM(M37:M43)');
//      $sheet->setCellValue('N36', '=SUM(N37:N43)');
//      $sheet->setCellValue('O36', '=SUM(O37:O43)');
//      $sheet->setCellValue('P36', '=SUM(P37:P43)');
//      $sheet->setCellValue('Q36', '=SUM(Q37:Q43)');
//      $sheet->setCellValue('R36', '=SUM(R37:R43)');

    $application->groupBy('payment_balance_article_id')->each(function ($item, $key) use ($sheet) {
      $source = $item->groupBy('source_id')->map(function ($item) {
        $item = $item->groupBy('activity_type_id')->map(function ($item) {
          return $item->sum('count');
        });
        $array = $item->toArray();

        return collect([
          '01' => array_key_exists('1', $array) ? round($item['1'] * 1.2 * 1000, 3) : 0,
          '21' => array_key_exists('2', $array) ? round($item['2'] * 1.2 * 1000, 3) : 0,
          '61' => array_key_exists('3', $array) ? round($item['3'] * 1.2 * 1000, 3) : 0,
          '81' => array_key_exists('4', $array) ? round($item['4'] * 1.2 * 1000, 3) : 0,
        ]);
      });

      if ($key <= 4) {
        $sheet->setCellValue('A' . (9 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (9 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (9 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (9 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (9 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (9 + $key), $source['1']['81']);
        $sheet->setCellValue('G' . (9 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81']);
        $sheet->setCellValue('H' . (9 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (9 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (9 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (9 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (9 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (9 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (9 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (9 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (9 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (9 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (9 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key <= 12) {
        $sheet->setCellValue('A' . (11 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (11 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (11 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (11 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (11 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (11 + $key), $source['1']['81']);
        $sheet->setCellValue('H' . (11 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (11 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (11 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (11 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (11 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (11 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (11 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (11 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (11 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (11 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key <= 23) {
        $sheet->setCellValue('A' . (12 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (12 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (12 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (12 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (12 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (12 + $key), $source['1']['81']);
        $sheet->setCellValue('H' . (12 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (12 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (12 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (12 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (12 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (12 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (12 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (12 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (12 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (12 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (12 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key > 23) {
        $sheet->setCellValue('A' . (13 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (13 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (13 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (13 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (13 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (13 + $key), $source['1']['81']);
        $sheet->setCellValue('H' . (13 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (13 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (13 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (13 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (13 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (13 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
//          $sheet->setCellValue('N' . (13 + $key), $source['3']['01']);
//          $sheet->setCellValue('O' . (13 + $key), $source['3']['21']);
//          $sheet->setCellValue('P' . (13 + $key), $source['3']['61']);
//          $sheet->setCellValue('Q' . (13 + $key), $source['3']['81']);
//          $sheet->setCellValue('R' . (13 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      }
    });

    ob_start();
    $this->writer->save('php://output');
    $content = ob_get_contents();
    ob_end_clean();

    Storage::disk('local')->put("public/table.xlsx", $content);

    return asset('storage/table.xlsx');
  }
}
