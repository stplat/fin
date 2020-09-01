<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
  public function budget()
  {
    return $this->belongsTo(Budget::class);
  }

  public function period()
  {
    return $this->belongsTo(Period::class);
  }

  public function source()
  {
    return $this->belongsTo(Source::class);
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
