<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','code', 'commission','fix_salary','expire','display_type','number','whatsapp', 'parent_affiliate_id','parent_affiliate_commission','membership_plan_id','status','expiry_date'];

    public function affiliate()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function parentAffiliate()
    {
        return $this->hasOne(User::class,'id','parent_affiliate_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_affiliate', 'affiliate_id', 'user_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function userAffiliate()
    {
        return $this->hasMany(UserAffiliate::class,'affiliate_id','user_id');
    }

    public function membershipPlan()
    {
        return $this->hasOne(MembershipPlan::class,'id','membership_plan_id');
    }
}
