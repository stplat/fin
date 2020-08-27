<?php

namespace App\Http\Controllers;

use App\Services\InvolvementService;
use App\Http\Requests\Involvement\InvolvementAll;

class InvolvementController extends Controller
{
  protected $involvementService;

  public function __construct(InvolvementService $involvementService)
  {
    $this->involvementService = $involvementService;
  }

  public function index()
  {
//    dd($this->involvementService->getInvolvement()->toArray());
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
}
