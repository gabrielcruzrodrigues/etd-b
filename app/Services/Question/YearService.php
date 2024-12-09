<?php

namespace App\Services\Question;

use App\Contracts\YearServiceContract;
use App\Models\Year\Year;

class YearService implements YearServiceContract
{
     public function getById(int $yearId)
     {
          return Year::findOrFail($yearId);
     }
}
