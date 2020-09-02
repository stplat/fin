<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Http\Requests\Application\ApplicationAll;

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
}
