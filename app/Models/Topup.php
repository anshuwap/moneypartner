<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Topup extends BaseModel
{
    use HasFactory;

    public function scopeAllCount($query)
    {
        return $query->where('retailer_id', Auth::user()->_id)->count();
    }

    public function scopeLikeColumn($query, $val)
    {
        $query->where('retailer_id', Auth::user()->_id);
        $query->where('name', 'like', "%$val%");
        return $query->count();
    }

    public function scopeGetResult($query, $val)
    {
        $query->where('retailer_id', Auth::user()->_id);
        $query->where('name', 'like', "%$val%");
        return $query->get();
    }


    public function RetailerName()
    {
        return $this->belongsTo('App\Models\User', 'retailer_id', '_id')->select('outlet_name');
    }
}
