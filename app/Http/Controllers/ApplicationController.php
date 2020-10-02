<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Http\Requests\Application\ApplicationAll;
use App\Http\Requests\Application\ApplicationUpdate;
use App\Http\Requests\Application\ApplicationUpload;

class ApplicationController extends Controller
{
  protected $applicationService;

  public function __construct(ApplicationService $applicationService)
  {
    $this->applicationService = $applicationService;
  }

  public function index()
  {
//    dd($this->applicationService->getApplications([1, 2], 1, 1, 2, 2, 2, 2)->toArray());
    return view('application')->with([
      'data' => collect([
        'periods' => $this->applicationService->getPeriods(),
        'versions' => $this->applicationService->getVersions(),
        'articles' => $this->applicationService->getArticles(),
      ])
    ]);
  }

  public function all(ApplicationAll $request)
  {
    $periods = $request->input('periods');
    $article = $request->input('article');
    $version = $request->input('version');
    $version_budget = $request->input('version_budget');
    $version_involvement = $request->input('version_involvement');
    $version_f22 = $request->input('version_f22');
    $version_shipment = $request->input('version_shipment');

    return $this->applicationService->getApplications($periods, $article, $version, $version_budget, $version_involvement, $version_f22, $version_shipment);
  }

  public function update(ApplicationUpdate $request)
  {
    $period = $request->input('period');
    $periods = $request->input('periods');
    $article = $request->input('article');
    $version = $request->input('version');
    $version_budget = $request->input('version_budget');
    $version_involvement = $request->input('version_involvement');
    $version_f22 = $request->input('version_f22');
    $version_shipment = $request->input('version_shipment');

    Application::where('period_id', $period)
      ->where('version_id', $version)
      ->where('source_id', $request->input('source'))
      ->where('activity_type_id', $request->input('activity'))
      ->where('payment_balance_article_id', $request->input('article'))
      ->where('dkre_id', $request->input('region'))
      ->update([
        $request->input('param') => $request->input('value')
      ]);

    return $this->applicationService->getApplications($periods, $article, $version, $version_budget, $version_involvement, $version_f22, $version_shipment);
  }

  /**
   * Обновляем данные из файла
   *
   * @param \App\Http\Requests\Budget\BudgetUpdate
   * @return \Illuminate\Support\Collection
   */
  public function upload(ApplicationUpload $request)
  {
    $file = $request->file('file');
    $periods = $request->input('periods');
    $article = $request->input('article');
    $version = $request->input('version');
    $version_budget = $request->input('version_budget');
    $version_involvement = $request->input('version_involvement');
    $version_f22 = $request->input('version_f22');
    $version_shipment = $request->input('version_shipment');
    $data = $this->applicationService->getUploadFile($file, $version);

    Application::where('version_id', $version)->delete();
    Application::insert($data);

    return $this->applicationService->getApplications($periods, $article, $version, $version_budget, $version_involvement, $version_f22, $version_shipment);
  }
}
