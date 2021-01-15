<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Http\Requests\Shipment\ShipmentAll;
use App\Http\Requests\Shipment\ShipmentUpload;

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
        'months' => $this->shipmentService->getPeriods(),
        'versions' => $this->shipmentService->getVersions(),
      ])
    ]);
  }

  public function all(ShipmentAll $request)
  {
    $regions = $request->input('regions') ?: null;
    $periods = $request->input('periods');
    $version = $request->input('version');

    return $this->shipmentService->getShipments($periods, $version, $regions);
  }

  /**
   * Обновляем данные из файла
   *
   * @param \App\Http\Requests\Budget\BudgetUpdate
   * @return \Illuminate\Support\Collection
   */
  public function upload(ShipmentUpload $request)
  {
    $file = $request->file('file');
    $regions = $request->input('regions') ?: null;
    $periods = is_array($request->input('periods')) ? $request->input('periods') : [$request->input('periods')];
    $version = $request->input('version');
    $data = $this->shipmentService->getUploadFile($file, $version);

    Shipment::where('version_id', $version)->delete();
    Shipment::insert($data);

    return $this->shipmentService->getShipments($periods, $version, $regions);
  }
}
