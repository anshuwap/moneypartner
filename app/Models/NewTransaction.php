<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewTransaction extends BaseModel
{
    use HasFactory;

    public function OutletName(){
        return $this->belongsTo('App\Models\Outlet', 'outlet_id', '_id')->select('outlet_name');
    }

     public function UserName(){
        return $this->belongsTo('App\Models\User', 'response.action_by', '_id')->select('full_name');
    }
}
