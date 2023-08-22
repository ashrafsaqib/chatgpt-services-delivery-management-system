<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortHoliday extends Model
{
    use HasFactory;
    
    protected $fillable = ['date', 'time_start', 'time_end', 'staff_id'];

    public function staff()
    {
        return $this->hasOne(user::class,'id','staff_id');
    }
}
