<?php

namespace App\Http\Controllers;

use App\Models\Involvement;
use App\Services\ShipmentService;
use App\Http\Requests\Involvement\InvolvementAll;
use App\Http\Requests\Involvement\InvolvementUpdate;

class ShipmentController extends Controller
{
  protected $shipmentService;

  public function __construct(ShipmentService $shipmentService)
  {
    $this->shipmentService = $shipmentService;
  }

  public function index()
  {
//    dd($this->shipmentService->getShipments([3,4,5,6,7,8,9,10,11,12,13,14], 1)->toArray());
    return view('shipment')->with([
      'data' => collect([
        'dkres' => $this->shipmentService->getDkres(),
        'regions' => $this->shipmentService->getRegions(),
        'months' => $this->shipmentService->getPeriods('month'),
        'versions' => $this->shipmentService->getVersions(),
      ])
    ]);
  }

  public function all(InvolvementAll $request)
  {
    $regions = $request->input('regions') ?: null;
    $periods = $request->input('periods');
    $version = $request->input('version');

    return $this->shipmentService->getShipments($periods, $version, $regions);
  }
}
