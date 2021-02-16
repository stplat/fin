<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMaterial extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'quantity'
  ];

  public function dkre()
  {
    return $this->belongsTo(Dkre::class);
  }

  public function material()
  {
    return $this->belongsTo(Material::class);
  }
}
