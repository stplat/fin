<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Involvement extends Model
{
  protected $fillable = [
    'dkre_id', 'period_id', 'activity_type_id', 'payment_balance_article_general', 'version_id',
    'involve_by_prepayment_last_year', 'involve_by_prepayment_current_year',
    'involve_by_turnover', 'prepayment_current_year', 'prepayment_next_year'
  ];

  public function period()
  {
    return $this->belongsTo(Period::class);
  }

  public function activity()
  {
    return $this->belongsTo(ActivityType::class, 'activity_type_id', 'id');
  }

  public function article()
  {
    return $this->belongsTo(PaymentBalanceArticle::class, 'payment_balance_article_id', 'id');
  }

  public function dkre()
  {
    return $this->belongsTo(Dkre::class);
  }

  public function version()
  {
    return $this->belongsTo(Version::class);
  }
}
