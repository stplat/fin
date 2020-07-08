<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dkre extends Model
{
  protected $table = 'dkre';

  public function budget()
  {
    return $this->hasMany(Budget::class);
  }
}
