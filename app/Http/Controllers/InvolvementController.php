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
//    dd($this->involvementService->getInvolvement([3], 1)->toArray());
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
    $regions = $request->input('regions');
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;

    return $this->involvementService->getInvolvement($periods, $version, $regions);
  }

  public function update(InvolvementUpdate $request) {
    return $request->input('period');
    Involvement::where('period_id', $request->input('period'))
      ->where('version_id', $request->input('version'))
      ->where('dkre_id', $request->input('region'))
      ->where('activity_id', $request->input('activity'))
      ->where('article_id', $request->input('article'))
      ->update()
  }
}
