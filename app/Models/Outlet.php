<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Outlet extends BaseModel
{
    use HasFactory;


    public function scopeAllCount($query)
    {

        if (Auth::user()->role == 'distributor') {
            $query->where('user_id', Auth::user()->_id);
        }
        return $query->count();
    }

    public function scopeLikeColumn($query, $val)
    {

        if (Auth::user()->role == 'distributor') {

            $query->where('user_id', Auth::user()->_id);
        }
        $query->where('retailer_name', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        $query->orWhere('outlet_name', 'like', "%$val%");
        $query->orWhere('state', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        return $query->count();
    }

    public function scopeGetResult($query, $val)
    {
        if (Auth::user()->role == 'distributor') {
            $query->where('user_id', Auth::user()->_id);
        }
        $query->where('retailer_name', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        $query->orWhere('outlet_name', 'like', "%$val%");
        $query->orWhere('state', 'like', "%$val%");
        $query->orWhere('mobile_no', 'like', "%$val%");
        return $query->get();
    }
}
