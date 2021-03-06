<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
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

  public function version()
  {
    return $this->belongsTo(Version::class);
  }

}
