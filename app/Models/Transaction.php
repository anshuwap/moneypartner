<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends BaseModel
{
    use HasFactory;

    public function OutletName(){
        return $this->belongsTo('App\Models\Outlet', 'outlet_id', '_id')->select('outlet_name');
    }
}
