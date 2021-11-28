<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends BaseModel
{
    use HasFactory;


    public function scopeAllCount($query){

    return $query->count();
    }

    public function scopeLikeColumn($query,$val){

        $query->where('retailer_name', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        $query->orWhere('outlet_name', 'like', "%$val%");
        $query->orWhere('state', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        return $query->count();
    }

    public function scopeGetResult($query,$val){

        $query->where('retailer_name', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        $query->orWhere('outlet_name', 'like', "%$val%");
        $query->orWhere('state', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        return $query->get();
    }
}
