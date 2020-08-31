<?php

namespace App\Http\Controllers;

use App\Models\Involvement;
use App\Services\InvolvementService;
use App\Http\Requests\Involvement\InvolvementAll;
use App\Http\Requests\Involvement\InvolvementUpdate;

class InvolvementController extends Controller
{
  protected $involvementService;

  public function __construct(InvolvementService $involvementService)
  {
    $this->involvementService = $involvementService;
  }

  public function index()
  {
    return view('involvement')->with([
      'data' => collect([
        'dkres' => $this->involvementService->getDkres(),
        'regions' => $this->involvementService->getRegions(),
        'months' => $this->involvementService->getPeriods('month'),
        'versions' => $this->involvementService->getVersions(),
      ])
    ]);
  }

  public function all(InvolvementAll $request)
  {
    $regions = $request->input('regions') ?: null;
    $periods = $request->input('periods');
    $version = $request->input('version');

    return $this->involvementService->getInvolvement($periods, $version, $regions);
  }

  public function update(InvolvementUpdate $request)
  {
    $region = $request->input('region');
    $regions = $request->input('regions') ?: null;
    $period = $request->input('period');
    $periods = $request->input('periods');
    $version = $request->input('version');

    Involvement::where('period_id', $period)
      ->where('version_id', $version)
      ->where('dkre_id', $region)
      ->where('activity_type_id', $request->input('activity'))
      ->where('payment_balance_article_id', $request->input('article'))
      ->update([
        $request->input('param') => $request->input('value')
      ]);

    return $this->involvementService->getInvolvement($periods, $version, $regions);
  }
}
