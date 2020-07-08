<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
  public function period()
  {
    return $this->belongsTo(Period::class);
  }

  public function vid_deyatelnosti()
  {
    return $this->belongsTo(VidDeyatelnosti::class);
  }

  public function statya_pb()
  {
    return $this->belongsTo(StatyaPb::class);
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
