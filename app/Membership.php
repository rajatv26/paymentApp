<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Membership extends Model
{
    protected $table = 'app_membership';
    
     public function customers()
    {
        return $this->hasMany('App\Customers','plan_id');
    }
}
