<?php

namespace Modules\Essay\Models;


use Illuminate\Database\Eloquent\Model;

class EssayUserRating extends Model
{
    protected $fillable = ['user_id', 'essay_id', 'title','status','essay_image_path','essay_text','final_note','general_comment','compentence_1','compentence_2','compentence_3','compentence_4','compentence_5',];
    
    public function essays()
    {
        return $this->hasMany(Essay::class);
    }
}
