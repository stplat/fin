<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use App\Http\Requests\BudgetAll;

class BudgetController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index()
  {
//    dd($this->budgetService->getBudgetByGroupDkre(1, 2)->toArray());
    return view('budget');
  }

  public function all(BudgetAll $request)
  {
    $period = $request->input('period');
    $version = $request->input('version');
    $is_dkre = $request->input('is_dkre');

    return $is_dkre ? $this->budgetService->getBudgetByGroupDkre($period, $version) : $this->budgetService->getBudget($period, $version);
  }
}
