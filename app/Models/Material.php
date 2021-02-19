<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'unused'
  ];

  public function period()
  {
    return $this->belongsTo(Period::class);
  }

  public function dkre()
  {
    return $this->belongsTo(Dkre::class);
  }

  public function order_materials()
  {
    return $this->hasMany(OrderMaterial::class);
  }

  public function article()
  {
    return $this->belongsTo(PaymentBalanceArticle::class, 'payment_balance_article_id', 'id');
  }
}
