<?php

namespace App\Models;

use App\Models\Dkre;
use App\Models\Period;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
  ];

  public function period()
  {
    return $this->belongsTo(Period::class);
  }

  public function dkre()
  {
    return $this->belongsTo(Dkre::class);
  }
}
