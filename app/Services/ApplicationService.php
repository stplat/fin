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
      $sheet->setTitle(str_replace('ДКРЭ_', '', $item[0]->dkre->region));
      $sheet->getStyle('A7:R45')->getBorders()->getAllBorders()->setBorderStyle('thin');
      $sheet->getStyle('C10:R45')->getNumberFormat()->setFormatCode('# ##0.000_-;#;-#;"---"');
      $sheet->getStyle('C10:R45')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C10:R45')->getAlignment()->setVertical('center');
      /* Заголовок */
      $sheet->setCellValue('A1', 'ЗАЯВКА');
      $sheet->getStyle('A1')->getFont()->setBold(true);
      $sheet->getStyle('A1')->getFont()->setSize('20');
      $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
      $sheet->mergeCells('A1:R1');
      /* Подзаголовок */
      $sheet->setCellValue('A2', 'на финансирование поставки материально-технических ресурсов на ' . $item[0]->period->name . ' 2020 г.');
      $sheet->getStyle('A2')->getFont()->setSize('20');
      $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
      $sheet->mergeCells('A2:R2');
      /* ДКРЭ */
      $sheet->setCellValue('A3', 'Дирекция капитального ремонта и реконструкции объектов электрификации и электроснабжения железных дорог (ДКРЭ)');
      $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
      $sheet->mergeCells('A3:R3');
      $sheet->setCellValue('B5', $item[0]->dkre->region);
      /* ЕИ */
      $sheet->setCellValue('A6', "(тыс.руб. с НДС)");
      $sheet->getStyle('A6')->getFont()->setSize('12');
      $sheet->getStyle('A6')->getAlignment()->setHorizontal('right');
      $sheet->mergeCells('A6:R6');
      /* Заголовки */
      $sheet->setCellValue('A7', '№ статьи');
      $sheet->mergeCells('A7:A9');
      $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('A7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('A')->setAutoSize(false);
      $sheet->getColumnDimension('A')->setWidth(9);
      /* Статьи */
      $sheet->setCellValue('B7', 'Наименование статей');
      $sheet->mergeCells('B7:B9');
      $sheet->getStyle('B7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('B7')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('B')->setAutoSize(false);
      $sheet->getColumnDimension('B')->setWidth(44);
      /* Период */
      $sheet->setCellValue('C7', $item[0]->period->name);
      $sheet->mergeCells('C7:R7');
      $sheet->getStyle('C7')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C7')->getAlignment()->setVertical('center');
      /* ЦЗ */
      $sheet->setCellValue('C8', 'Централизованная поставка');
      $sheet->mergeCells('C8:F8');
      $sheet->getStyle('C8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('C')->setAutoSize(false);
      $sheet->getColumnDimension('C')->setWidth(14);
      /* Итого */
      $sheet->setCellValue('G8', 'Итого');
      $sheet->mergeCells('G8:G9');
      $sheet->getStyle('G8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('G8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('G')->setAutoSize(false);
      $sheet->getColumnDimension('G')->setWidth(14);
      /* ПЕР */
      $sheet->setCellValue('C9', 'Перевозки');
      $sheet->getStyle('C9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('C9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('C')->setAutoSize(false);
      $sheet->getColumnDimension('C')->setWidth(14);
      /* ПВД */
      $sheet->setCellValue('D9', 'ПВД');
      $sheet->getStyle('D9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('D9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('D')->setAutoSize(false);
      $sheet->getColumnDimension('D')->setWidth(14);
      /* ИНВ */
      $sheet->setCellValue('E9', 'КВ');
      $sheet->getStyle('E9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('E9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('E')->setAutoSize(false);
      $sheet->getColumnDimension('E')->setWidth(14);
      /* ПРО */
      $sheet->setCellValue('F9', 'Прочие');
      $sheet->getStyle('F9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('F9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('F')->setAutoSize(false);
      $sheet->getColumnDimension('F')->setWidth(14);
      /* СЗ */
      $sheet->setCellValue('H8', 'Самостоятельная закупка');
      $sheet->mergeCells('H8:K8');
      $sheet->getStyle('H8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('H8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('H')->setAutoSize(false);
      $sheet->getColumnDimension('H')->setWidth(14);
      /* Итого */
      $sheet->setCellValue('L8', 'Итого');
      $sheet->mergeCells('L8:L9');
      $sheet->getStyle('L8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('L8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('L')->setAutoSize(false);
      $sheet->getColumnDimension('L')->setWidth(14);
      /* ПЕР */
      $sheet->setCellValue('H9', 'Перевозки');
      $sheet->getStyle('H9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('H9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('H')->setAutoSize(false);
      $sheet->getColumnDimension('H')->setWidth(14);
      /* ПВД */
      $sheet->setCellValue('I9', 'ПВД');
      $sheet->getStyle('I9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('I9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('I')->setAutoSize(false);
      $sheet->getColumnDimension('I')->setWidth(14);
      /* ИНВ */
      $sheet->setCellValue('J9', 'КВ');
      $sheet->getStyle('J9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('J9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('J')->setAutoSize(false);
      $sheet->getColumnDimension('J')->setWidth(14);
      /* ПРО */
      $sheet->setCellValue('K9', 'Прочие');
      $sheet->getStyle('K9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('K9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('K')->setAutoSize(false);
      $sheet->getColumnDimension('K')->setWidth(14);
      /* ВСЕГО */
      $sheet->setCellValue('M8', 'ВСЕГО');
      $sheet->mergeCells('M8:M9');
      $sheet->getStyle('M8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('M8')->getAlignment()->setVertical('center');
      $sheet->getStyle('M8')->getFont()->setBold(true);
      $sheet->getColumnDimension('M')->setAutoSize(false);
      $sheet->getColumnDimension('M')->setWidth(14);
      /* ЧДФ */
      $sheet->setCellValue('N8', 'Закупка через другие филиалы');
      $sheet->mergeCells('N8:Q8');
      $sheet->getStyle('N8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('N8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('N')->setAutoSize(false);
      $sheet->getColumnDimension('N')->setWidth(14);
      /* Итого */
      $sheet->setCellValue('R8', 'Итого');
      $sheet->mergeCells('R8:R9');
      $sheet->getStyle('R8')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('R8')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('R')->setAutoSize(false);
      $sheet->getColumnDimension('R')->setWidth(14);
      /* ПЕР */
      $sheet->setCellValue('N9', 'Перевозки');
      $sheet->getStyle('N9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('N9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('N')->setAutoSize(false);
      $sheet->getColumnDimension('N')->setWidth(14);
      /* ПВД */
      $sheet->setCellValue('O9', 'ПВД');
      $sheet->getStyle('O9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('O9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('O')->setAutoSize(false);
      $sheet->getColumnDimension('O')->setWidth(14);
      /* ИНВ */
      $sheet->setCellValue('P9', 'КВ');
      $sheet->getStyle('P9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('P9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('P')->setAutoSize(false);
      $sheet->getColumnDimension('P')->setWidth(14);
      /* ПРО */
      $sheet->setCellValue('Q9', 'Прочие');
      $sheet->getStyle('Q9')->getAlignment()->setHorizontal('center');
      $sheet->getStyle('Q9')->getAlignment()->setVertical('center');
      $sheet->getColumnDimension('Q')->setAutoSize(false);
      $sheet->getColumnDimension('Q')->setWidth(14);

      /* Таблица с данными*/
      $sheet->setCellValue('A10', '');
      $sheet->setCellValue('B10', 'ВСЕГО РЖДС, в том числе:');
      $sheet->setCellValue('C10', '=C11+C16');
      $sheet->setCellValue('D10', '=D11+D16');
      $sheet->setCellValue('E10', '=E11+E16');
      $sheet->setCellValue('F10', '=F11+F16');
      $sheet->setCellValue('G10', '=G11+G16');
      $sheet->setCellValue('H10', '=H11+H16');
      $sheet->setCellValue('I10', '=I11+I16');
      $sheet->setCellValue('J10', '=J11+J16');
      $sheet->setCellValue('K10', '=K11+K16');
      $sheet->setCellValue('L10', '=L11+L16');
      $sheet->setCellValue('M10', '=M11+M16');
      $sheet->setCellValue('N10', '=N11+N16');
      $sheet->setCellValue('O10', '=O11+O16');
      $sheet->setCellValue('P10', '=P11+P16');
      $sheet->setCellValue('Q10', '=Q11+Q16');
      $sheet->setCellValue('R10', '=R11+R16');

      $sheet->setCellValue('A11', '63300');
      $sheet->setCellValue('B11', 'ТОПЛИВО ВСЕГО');
      $sheet->setCellValue('C11', '=SUM(C12:C15)');
      $sheet->setCellValue('D11', '=SUM(D12:D15)');
      $sheet->setCellValue('E11', '=SUM(E12:E15)');
      $sheet->setCellValue('F11', '=SUM(F12:F15)');
      $sheet->setCellValue('G11', '=SUM(G12:G15)');
      $sheet->setCellValue('H11', '=SUM(H12:H15)');
      $sheet->setCellValue('I11', '=SUM(I12:I15)');
      $sheet->setCellValue('J11', '=SUM(J12:J15)');
      $sheet->setCellValue('K11', '=SUM(K12:K15)');
      $sheet->setCellValue('L11', '=SUM(L12:L15)');
      $sheet->setCellValue('M11', '=SUM(M12:M15)');
      $sheet->setCellValue('N11', '=SUM(N12:N15)');
      $sheet->setCellValue('O11', '=SUM(O12:O15)');
      $sheet->setCellValue('P11', '=SUM(P12:P15)');
      $sheet->setCellValue('Q11', '=SUM(Q12:Q15)');
      $sheet->setCellValue('R11', '=SUM(R12:R15)');

      $sheet->setCellValue('A16', '63400');
      $sheet->setCellValue('B16', 'МАТЕРИАЛЫ ВСЕГО');
      $sheet->setCellValue('C16', '=C17+C26+C38+SUM(C33:C37)');
      $sheet->setCellValue('D16', '=D17+D26+D38+SUM(D33:D37)');
      $sheet->setCellValue('E16', '=E17+E26+E38+SUM(E33:E37)');
      $sheet->setCellValue('F16', '=F17+F26+F38+SUM(F33:F37)');
      $sheet->setCellValue('G16', '=G17+G26+G38+SUM(G33:G37)');
      $sheet->setCellValue('H16', '=H17+H26+H38+SUM(H33:H37)');
      $sheet->setCellValue('I16', '=I17+I26+I38+SUM(I33:I37)');
      $sheet->setCellValue('J16', '=J17+J26+J38+SUM(J33:J37)');
      $sheet->setCellValue('K16', '=K17+K26+K38+SUM(K33:K37)');
      $sheet->setCellValue('L16', '=L17+L26+L38+SUM(L33:L37)');
      $sheet->setCellValue('M16', '=M17+M26+M38+SUM(M33:M37)');
      $sheet->setCellValue('N16', '=N17+N26+N38+SUM(N33:N37)');
      $sheet->setCellValue('O16', '=O17+O26+O38+SUM(O33:O37)');
      $sheet->setCellValue('P16', '=P17+P26+P38+SUM(P33:P37)');
      $sheet->setCellValue('Q16', '=Q17+Q26+Q38+SUM(Q33:Q37)');
      $sheet->setCellValue('R16', '=R17+R26+R38+SUM(R33:R37)');

      $sheet->setCellValue('A17', '63410');
      $sheet->setCellValue('B17', 'материалы верхнего строения пути в т.ч.:');
      $sheet->setCellValue('C17', '=SUM(C18:C25)');
      $sheet->setCellValue('D17', '=SUM(D18:D25)');
      $sheet->setCellValue('E17', '=SUM(E18:E25)');
      $sheet->setCellValue('F17', '=SUM(F18:F25)');
      $sheet->setCellValue('G17', '=SUM(G18:G25)');
      $sheet->setCellValue('H17', '=SUM(H18:H25)');
      $sheet->setCellValue('I17', '=SUM(I18:I25)');
      $sheet->setCellValue('J17', '=SUM(J18:J25)');
      $sheet->setCellValue('K17', '=SUM(K18:K25)');
      $sheet->setCellValue('L17', '=SUM(L18:L25)');
      $sheet->setCellValue('M17', '=SUM(M18:M25)');
      $sheet->setCellValue('N17', '=SUM(N18:N25)');
      $sheet->setCellValue('O17', '=SUM(O18:O25)');
      $sheet->setCellValue('P17', '=SUM(P18:P25)');
      $sheet->setCellValue('Q17', '=SUM(Q18:Q25)');
      $sheet->setCellValue('R17', '=SUM(R18:R25)');

      $sheet->setCellValue('A26', '63420');
      $sheet->setCellValue('B26', 'запасные части, узлы и литые детали подвижного состава в т.ч.:');
      $sheet->setCellValue('C26', '=SUM(C27:C32)');
      $sheet->setCellValue('D26', '=SUM(D27:D32)');
      $sheet->setCellValue('E26', '=SUM(E27:E32)');
      $sheet->setCellValue('F26', '=SUM(F27:F32)');
      $sheet->setCellValue('G26', '=SUM(G27:G32)');
      $sheet->setCellValue('H26', '=SUM(H27:H32)');
      $sheet->setCellValue('I26', '=SUM(I27:I32)');
      $sheet->setCellValue('J26', '=SUM(J27:J32)');
      $sheet->setCellValue('K26', '=SUM(K27:K32)');
      $sheet->setCellValue('L26', '=SUM(L27:L32)');
      $sheet->setCellValue('M26', '=SUM(M27:M32)');
      $sheet->setCellValue('N26', '=SUM(N27:N32)');
      $sheet->setCellValue('O26', '=SUM(O27:O32)');
      $sheet->setCellValue('P26', '=SUM(P27:P32)');
      $sheet->setCellValue('Q26', '=SUM(Q27:Q32)');
      $sheet->setCellValue('R26', '=SUM(R27:R32)');

      $sheet->setCellValue('A38', '63490');
      $sheet->setCellValue('B38', 'прочие материалы, в т.ч.:');
      $sheet->setCellValue('C38', '=SUM(C39:C45)');
      $sheet->setCellValue('D38', '=SUM(D39:D45)');
      $sheet->setCellValue('E38', '=SUM(E39:E45)');
      $sheet->setCellValue('F38', '=SUM(F39:F45)');
      $sheet->setCellValue('G38', '=SUM(G39:G45)');
      $sheet->setCellValue('H38', '=SUM(H39:H45)');
      $sheet->setCellValue('I38', '=SUM(I39:I45)');
      $sheet->setCellValue('J38', '=SUM(J39:J45)');
      $sheet->setCellValue('K38', '=SUM(K39:K45)');
      $sheet->setCellValue('L38', '=SUM(L39:L45)');
      $sheet->setCellValue('M38', '=SUM(M39:M45)');
      $sheet->setCellValue('N38', '=SUM(N39:N45)');
      $sheet->setCellValue('O38', '=SUM(O39:O45)');
      $sheet->setCellValue('P38', '=SUM(P39:P45)');
      $sheet->setCellValue('Q38', '=SUM(Q39:Q45)');
      $sheet->setCellValue('R38', '=SUM(R39:R45)');

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
          $sheet->setCellValue('A' . (11 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (11 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (11 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (11 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (11 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (11 + $key), $source['1']['81']);
          $sheet->setCellValue('G' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81']);
          $sheet->setCellValue('H' . (11 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (11 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (11 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (11 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (11 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
          $sheet->setCellValue('N' . (11 + $key), $source['3']['01']);
          $sheet->setCellValue('O' . (11 + $key), $source['3']['21']);
          $sheet->setCellValue('P' . (11 + $key), $source['3']['61']);
          $sheet->setCellValue('Q' . (11 + $key), $source['3']['81']);
          $sheet->setCellValue('R' . (11 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key <= 12) {
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
          $sheet->setCellValue('N' . (13 + $key), $source['3']['01']);
          $sheet->setCellValue('O' . (13 + $key), $source['3']['21']);
          $sheet->setCellValue('P' . (13 + $key), $source['3']['61']);
          $sheet->setCellValue('Q' . (13 + $key), $source['3']['81']);
          $sheet->setCellValue('R' . (13 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key <= 23) {
          $sheet->setCellValue('A' . (14 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (14 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (14 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (14 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (14 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (14 + $key), $source['1']['81']);
          $sheet->setCellValue('H' . (14 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (14 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (14 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (14 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (14 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (14 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
          $sheet->setCellValue('N' . (14 + $key), $source['3']['01']);
          $sheet->setCellValue('O' . (14 + $key), $source['3']['21']);
          $sheet->setCellValue('P' . (14 + $key), $source['3']['61']);
          $sheet->setCellValue('Q' . (14 + $key), $source['3']['81']);
          $sheet->setCellValue('R' . (14 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        } else if ($key > 23) {
          $sheet->setCellValue('A' . (15 + $key), $item[0]->article->code);
          $sheet->setCellValue('B' . (15 + $key), $item[0]->article->name);
          $sheet->setCellValue('C' . (15 + $key), $source['1']['01']);
          $sheet->setCellValue('D' . (15 + $key), $source['1']['21']);
          $sheet->setCellValue('E' . (15 + $key), $source['1']['61']);
          $sheet->setCellValue('F' . (15 + $key), $source['1']['81']);
          $sheet->setCellValue('H' . (15 + $key), $source['2']['01']);
          $sheet->setCellValue('I' . (15 + $key), $source['2']['21']);
          $sheet->setCellValue('J' . (15 + $key), $source['2']['61']);
          $sheet->setCellValue('K' . (15 + $key), $source['2']['81']);
          $sheet->setCellValue('L' . (15 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
          $sheet->setCellValue('M' . (15 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
            $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
          $sheet->setCellValue('N' . (15 + $key), $source['3']['01']);
          $sheet->setCellValue('O' . (15 + $key), $source['3']['21']);
          $sheet->setCellValue('P' . (15 + $key), $source['3']['61']);
          $sheet->setCellValue('Q' . (15 + $key), $source['3']['81']);
          $sheet->setCellValue('R' . (15 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        }
      });
    });

    /* Формируем сводный лист */
    $length = $application->groupBy('dkre_id')->count();
    $this->spreadsheet->createSheet($length);
    $this->spreadsheet->setActiveSheetIndex($length);
    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->getSheetView()->setZoomScale(75);
    $sheet->setTitle(str_replace('ДКРЭ_', '', 'ВСЕГО'));
    $sheet->getStyle('A7:R45')->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('C10:R45')->getNumberFormat()->setFormatCode('# ##0.000_-;#;-#;"---"');
    $sheet->getStyle('C10:R45')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C10:R45')->getAlignment()->setVertical('center');
    /* Заголовок */
    $sheet->setCellValue('A1', 'ЗАЯВКА');
    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->getStyle('A1')->getFont()->setSize('20');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('A1:R1');
    /* Подзаголовок */
    $sheet->setCellValue('A2', "на финансирование поставки материально-технических ресурсов на 2020 г.");
    $sheet->getStyle('A2')->getFont()->setSize('20');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('A2:R2');
    /* ДКРЭ */
    $sheet->setCellValue('A3', 'Дирекция капитального ремонта и реконструкции объектов электрификации и электроснабжения железных дорог (ДКРЭ)');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('A3:R3');
    $sheet->setCellValue('B5', 'ВСЕГО');
    /* ЕИ */
    $sheet->setCellValue('A6', "(тыс.руб. с НДС)");
    $sheet->getStyle('A6')->getFont()->setSize('12');
    $sheet->getStyle('A6')->getAlignment()->setHorizontal('right');
    $sheet->mergeCells('A6:R6');
    /* Заголовки */
    $sheet->setCellValue('A7', '№ статьи');
    $sheet->mergeCells('A7:A9');
    $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('A')->setAutoSize(false);
    $sheet->getColumnDimension('A')->setWidth(9);
    /* Статьи */
    $sheet->setCellValue('B7', 'Наименование статей');
    $sheet->mergeCells('B7:B9');
    $sheet->getStyle('B7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B7')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('B')->setAutoSize(false);
    $sheet->getColumnDimension('B')->setWidth(44);
    /* Период */
    $sheet->setCellValue('C7', '');
    $sheet->mergeCells('C7:R7');
    $sheet->getStyle('C7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C7')->getAlignment()->setVertical('center');
    /* ЦЗ */
    $sheet->setCellValue('C8', 'Централизованная поставка');
    $sheet->mergeCells('C8:F8');
    $sheet->getStyle('C8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('C')->setAutoSize(false);
    $sheet->getColumnDimension('C')->setWidth(14);
    /* Итого */
    $sheet->setCellValue('G8', 'Итого');
    $sheet->mergeCells('G8:G9');
    $sheet->getStyle('G8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('G8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('G')->setAutoSize(false);
    $sheet->getColumnDimension('G')->setWidth(14);
    /* ПЕР */
    $sheet->setCellValue('C9', 'Перевозки');
    $sheet->getStyle('C9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('C9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('C')->setAutoSize(false);
    $sheet->getColumnDimension('C')->setWidth(14);
    /* ПВД */
    $sheet->setCellValue('D9', 'ПВД');
    $sheet->getStyle('D9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('D9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('D')->setAutoSize(false);
    $sheet->getColumnDimension('D')->setWidth(14);
    /* ИНВ */
    $sheet->setCellValue('E9', 'КВ');
    $sheet->getStyle('E9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('E9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('E')->setAutoSize(false);
    $sheet->getColumnDimension('E')->setWidth(14);
    /* ПРО */
    $sheet->setCellValue('F9', 'Прочие');
    $sheet->getStyle('F9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('F9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('F')->setAutoSize(false);
    $sheet->getColumnDimension('F')->setWidth(14);
    /* СЗ */
    $sheet->setCellValue('H8', 'Самостоятельная закупка');
    $sheet->mergeCells('H8:K8');
    $sheet->getStyle('H8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('H8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('H')->setAutoSize(false);
    $sheet->getColumnDimension('H')->setWidth(14);
    /* Итого */
    $sheet->setCellValue('L8', 'Итого');
    $sheet->mergeCells('L8:L9');
    $sheet->getStyle('L8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('L8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('L')->setAutoSize(false);
    $sheet->getColumnDimension('L')->setWidth(14);
    /* ПЕР */
    $sheet->setCellValue('H9', 'Перевозки');
    $sheet->getStyle('H9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('H9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('H')->setAutoSize(false);
    $sheet->getColumnDimension('H')->setWidth(14);
    /* ПВД */
    $sheet->setCellValue('I9', 'ПВД');
    $sheet->getStyle('I9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('I9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('I')->setAutoSize(false);
    $sheet->getColumnDimension('I')->setWidth(14);
    /* ИНВ */
    $sheet->setCellValue('J9', 'КВ');
    $sheet->getStyle('J9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('J9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('J')->setAutoSize(false);
    $sheet->getColumnDimension('J')->setWidth(14);
    /* ПРО */
    $sheet->setCellValue('K9', 'Прочие');
    $sheet->getStyle('K9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('K9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('K')->setAutoSize(false);
    $sheet->getColumnDimension('K')->setWidth(14);
    /* ВСЕГО */
    $sheet->setCellValue('M8', 'ВСЕГО');
    $sheet->mergeCells('M8:M9');
    $sheet->getStyle('M8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('M8')->getAlignment()->setVertical('center');
    $sheet->getStyle('M8')->getFont()->setBold(true);
    $sheet->getColumnDimension('M')->setAutoSize(false);
    $sheet->getColumnDimension('M')->setWidth(14);
    /* ЧДФ */
    $sheet->setCellValue('N8', 'Закупка через другие филиалы');
    $sheet->mergeCells('N8:Q8');
    $sheet->getStyle('N8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('N8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('N')->setAutoSize(false);
    $sheet->getColumnDimension('N')->setWidth(14);
    /* Итого */
    $sheet->setCellValue('R8', 'Итого');
    $sheet->mergeCells('R8:R9');
    $sheet->getStyle('R8')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('R8')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('R')->setAutoSize(false);
    $sheet->getColumnDimension('R')->setWidth(14);
    /* ПЕР */
    $sheet->setCellValue('N9', 'Перевозки');
    $sheet->getStyle('N9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('N9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('N')->setAutoSize(false);
    $sheet->getColumnDimension('N')->setWidth(14);
    /* ПВД */
    $sheet->setCellValue('O9', 'ПВД');
    $sheet->getStyle('O9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('O9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('O')->setAutoSize(false);
    $sheet->getColumnDimension('O')->setWidth(14);
    /* ИНВ */
    $sheet->setCellValue('P9', 'КВ');
    $sheet->getStyle('P9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('P9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('P')->setAutoSize(false);
    $sheet->getColumnDimension('P')->setWidth(14);
    /* ПРО */
    $sheet->setCellValue('Q9', 'Прочие');
    $sheet->getStyle('Q9')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('Q9')->getAlignment()->setVertical('center');
    $sheet->getColumnDimension('Q')->setAutoSize(false);
    $sheet->getColumnDimension('Q')->setWidth(14);

    $sheet->setCellValue('A10', '');
    $sheet->setCellValue('B10', 'ВСЕГО РЖДС, в том числе:');
    $sheet->setCellValue('C10', '=C11+C16');
    $sheet->setCellValue('D10', '=D11+D16');
    $sheet->setCellValue('E10', '=E11+E16');
    $sheet->setCellValue('F10', '=F11+F16');
    $sheet->setCellValue('G10', '=G11+G16');
    $sheet->setCellValue('H10', '=H11+H16');
    $sheet->setCellValue('I10', '=I11+I16');
    $sheet->setCellValue('J10', '=J11+J16');
    $sheet->setCellValue('K10', '=K11+K16');
    $sheet->setCellValue('L10', '=L11+L16');
    $sheet->setCellValue('M10', '=M11+M16');
    $sheet->setCellValue('N10', '=N11+N16');
    $sheet->setCellValue('O10', '=O11+O16');
    $sheet->setCellValue('P10', '=P11+P16');
    $sheet->setCellValue('Q10', '=Q11+Q16');
    $sheet->setCellValue('R10', '=R11+R16');

    $sheet->setCellValue('A11', '63300');
    $sheet->setCellValue('B11', 'ТОПЛИВО ВСЕГО');
    $sheet->setCellValue('C11', '=SUM(C12:C15)');
    $sheet->setCellValue('D11', '=SUM(D12:D15)');
    $sheet->setCellValue('E11', '=SUM(E12:E15)');
    $sheet->setCellValue('F11', '=SUM(F12:F15)');
    $sheet->setCellValue('G11', '=SUM(G12:G15)');
    $sheet->setCellValue('H11', '=SUM(H12:H15)');
    $sheet->setCellValue('I11', '=SUM(I12:I15)');
    $sheet->setCellValue('J11', '=SUM(J12:J15)');
    $sheet->setCellValue('K11', '=SUM(K12:K15)');
    $sheet->setCellValue('L11', '=SUM(L12:L15)');
    $sheet->setCellValue('M11', '=SUM(M12:M15)');
    $sheet->setCellValue('N11', '=SUM(N12:N15)');
    $sheet->setCellValue('O11', '=SUM(O12:O15)');
    $sheet->setCellValue('P11', '=SUM(P12:P15)');
    $sheet->setCellValue('Q11', '=SUM(Q12:Q15)');
    $sheet->setCellValue('R11', '=SUM(R12:R15)');

    $sheet->setCellValue('A16', '63400');
    $sheet->setCellValue('B16', 'МАТЕРИАЛЫ ВСЕГО');
    $sheet->setCellValue('C16', '=C17+C26+C38+SUM(C33:C37)');
    $sheet->setCellValue('D16', '=D17+D26+D38+SUM(D33:D37)');
    $sheet->setCellValue('E16', '=E17+E26+E38+SUM(E33:E37)');
    $sheet->setCellValue('F16', '=F17+F26+F38+SUM(F33:F37)');
    $sheet->setCellValue('G16', '=G17+G26+G38+SUM(G33:G37)');
    $sheet->setCellValue('H16', '=H17+H26+H38+SUM(H33:H37)');
    $sheet->setCellValue('I16', '=I17+I26+I38+SUM(I33:I37)');
    $sheet->setCellValue('J16', '=J17+J26+J38+SUM(J33:J37)');
    $sheet->setCellValue('K16', '=K17+K26+K38+SUM(K33:K37)');
    $sheet->setCellValue('L16', '=L17+L26+L38+SUM(L33:L37)');
    $sheet->setCellValue('M16', '=M17+M26+M38+SUM(M33:M37)');
    $sheet->setCellValue('N16', '=N17+N26+N38+SUM(N33:N37)');
    $sheet->setCellValue('O16', '=O17+O26+O38+SUM(O33:O37)');
    $sheet->setCellValue('P16', '=P17+P26+P38+SUM(P33:P37)');
    $sheet->setCellValue('Q16', '=Q17+Q26+Q38+SUM(Q33:Q37)');
    $sheet->setCellValue('R16', '=R17+R26+R38+SUM(R33:R37)');

    $sheet->setCellValue('A17', '63410');
    $sheet->setCellValue('B17', 'материалы верхнего строения пути в т.ч.:');
    $sheet->setCellValue('C17', '=SUM(C18:C25)');
    $sheet->setCellValue('D17', '=SUM(D18:D25)');
    $sheet->setCellValue('E17', '=SUM(E18:E25)');
    $sheet->setCellValue('F17', '=SUM(F18:F25)');
    $sheet->setCellValue('G17', '=SUM(G18:G25)');
    $sheet->setCellValue('H17', '=SUM(H18:H25)');
    $sheet->setCellValue('I17', '=SUM(I18:I25)');
    $sheet->setCellValue('J17', '=SUM(J18:J25)');
    $sheet->setCellValue('K17', '=SUM(K18:K25)');
    $sheet->setCellValue('L17', '=SUM(L18:L25)');
    $sheet->setCellValue('M17', '=SUM(M18:M25)');
    $sheet->setCellValue('N17', '=SUM(N18:N25)');
    $sheet->setCellValue('O17', '=SUM(O18:O25)');
    $sheet->setCellValue('P17', '=SUM(P18:P25)');
    $sheet->setCellValue('Q17', '=SUM(Q18:Q25)');
    $sheet->setCellValue('R17', '=SUM(R18:R25)');

    $sheet->setCellValue('A26', '63420');
    $sheet->setCellValue('B26', 'запасные части, узлы и литые детали подвижного состава в т.ч.:');
    $sheet->setCellValue('C26', '=SUM(C27:C32)');
    $sheet->setCellValue('D26', '=SUM(D27:D32)');
    $sheet->setCellValue('E26', '=SUM(E27:E32)');
    $sheet->setCellValue('F26', '=SUM(F27:F32)');
    $sheet->setCellValue('G26', '=SUM(G27:G32)');
    $sheet->setCellValue('H26', '=SUM(H27:H32)');
    $sheet->setCellValue('I26', '=SUM(I27:I32)');
    $sheet->setCellValue('J26', '=SUM(J27:J32)');
    $sheet->setCellValue('K26', '=SUM(K27:K32)');
    $sheet->setCellValue('L26', '=SUM(L27:L32)');
    $sheet->setCellValue('M26', '=SUM(M27:M32)');
    $sheet->setCellValue('N26', '=SUM(N27:N32)');
    $sheet->setCellValue('O26', '=SUM(O27:O32)');
    $sheet->setCellValue('P26', '=SUM(P27:P32)');
    $sheet->setCellValue('Q26', '=SUM(Q27:Q32)');
    $sheet->setCellValue('R26', '=SUM(R27:R32)');

    $sheet->setCellValue('A38', '63490');
    $sheet->setCellValue('B38', 'прочие материалы, в т.ч.:');
    $sheet->setCellValue('C38', '=SUM(C39:C45)');
    $sheet->setCellValue('D38', '=SUM(D39:D45)');
    $sheet->setCellValue('E38', '=SUM(E39:E45)');
    $sheet->setCellValue('F38', '=SUM(F39:F45)');
    $sheet->setCellValue('G38', '=SUM(G39:G45)');
    $sheet->setCellValue('H38', '=SUM(H39:H45)');
    $sheet->setCellValue('I38', '=SUM(I39:I45)');
    $sheet->setCellValue('J38', '=SUM(J39:J45)');
    $sheet->setCellValue('K38', '=SUM(K39:K45)');
    $sheet->setCellValue('L38', '=SUM(L39:L45)');
    $sheet->setCellValue('M38', '=SUM(M39:M45)');
    $sheet->setCellValue('N38', '=SUM(N39:N45)');
    $sheet->setCellValue('O38', '=SUM(O39:O45)');
    $sheet->setCellValue('P38', '=SUM(P39:P45)');
    $sheet->setCellValue('Q38', '=SUM(Q39:Q45)');
    $sheet->setCellValue('R38', '=SUM(R39:R45)');

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
        $sheet->setCellValue('A' . (11 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (11 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (11 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (11 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (11 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (11 + $key), $source['1']['81']);
        $sheet->setCellValue('G' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81']);
        $sheet->setCellValue('H' . (11 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (11 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (11 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (11 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (11 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (11 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        $sheet->setCellValue('N' . (11 + $key), $source['3']['01']);
        $sheet->setCellValue('O' . (11 + $key), $source['3']['21']);
        $sheet->setCellValue('P' . (11 + $key), $source['3']['61']);
        $sheet->setCellValue('Q' . (11 + $key), $source['3']['81']);
        $sheet->setCellValue('R' . (11 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key <= 12) {
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
        $sheet->setCellValue('N' . (13 + $key), $source['3']['01']);
        $sheet->setCellValue('O' . (13 + $key), $source['3']['21']);
        $sheet->setCellValue('P' . (13 + $key), $source['3']['61']);
        $sheet->setCellValue('Q' . (13 + $key), $source['3']['81']);
        $sheet->setCellValue('R' . (13 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key <= 23) {
        $sheet->setCellValue('A' . (14 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (14 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (14 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (14 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (14 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (14 + $key), $source['1']['81']);
        $sheet->setCellValue('H' . (14 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (14 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (14 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (14 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (14 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (14 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        $sheet->setCellValue('N' . (14 + $key), $source['3']['01']);
        $sheet->setCellValue('O' . (14 + $key), $source['3']['21']);
        $sheet->setCellValue('P' . (14 + $key), $source['3']['61']);
        $sheet->setCellValue('Q' . (14 + $key), $source['3']['81']);
        $sheet->setCellValue('R' . (14 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
      } else if ($key > 23) {
        $sheet->setCellValue('A' . (15 + $key), $item[0]->article->code);
        $sheet->setCellValue('B' . (15 + $key), $item[0]->article->name);
        $sheet->setCellValue('C' . (15 + $key), $source['1']['01']);
        $sheet->setCellValue('D' . (15 + $key), $source['1']['21']);
        $sheet->setCellValue('E' . (15 + $key), $source['1']['61']);
        $sheet->setCellValue('F' . (15 + $key), $source['1']['81']);
        $sheet->setCellValue('H' . (15 + $key), $source['2']['01']);
        $sheet->setCellValue('I' . (15 + $key), $source['2']['21']);
        $sheet->setCellValue('J' . (15 + $key), $source['2']['61']);
        $sheet->setCellValue('K' . (15 + $key), $source['2']['81']);
        $sheet->setCellValue('L' . (15 + $key), $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81']);
        $sheet->setCellValue('M' . (15 + $key), $source['1']['01'] + $source['1']['21'] + $source['1']['61'] + $source['1']['81'] +
          $source['2']['01'] + $source['2']['21'] + $source['2']['61'] + $source['2']['81'] + $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
        $sheet->setCellValue('N' . (15 + $key), $source['3']['01']);
        $sheet->setCellValue('O' . (15 + $key), $source['3']['21']);
        $sheet->setCellValue('P' . (15 + $key), $source['3']['61']);
        $sheet->setCellValue('Q' . (15 + $key), $source['3']['81']);
        $sheet->setCellValue('R' . (15 + $key), $source['3']['01'] + $source['3']['21'] + $source['3']['61'] + $source['3']['81']);
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
