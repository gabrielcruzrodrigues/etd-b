<?php
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\User;

class CreditPurchaseRecord extends Model
{
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
