<?php

namespace Modules\Essay\Models;


use Illuminate\Database\Eloquent\Model;

class Essay extends Model
{
    protected $fillable = ['send_date', 'essay_theme', 'link_file'];
    

    public function essayuserrating()
    {
      return $this->belongsTo(EssayUserRating::class);   
    }
}
