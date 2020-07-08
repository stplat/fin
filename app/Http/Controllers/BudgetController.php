<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index()
  {
    return view('budget')->with([
      'data' => $this->budgetService->getBudgetByGroupDkre(1, 2)
    ]);
  }
}
