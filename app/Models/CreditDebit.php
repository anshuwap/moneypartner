<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditDebit extends BaseModel
{
    use HasFactory;

    public function RetailerName()
    {
        return $this->belongsTo('App\Models\User', 'retailer_id', '_id')->select('outlet_name');
    }

    public function OutletName()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id', '_id')->select('outlet_name');
    }

    public function UserName()
    {
        return $this->belongsTo('App\Models\User', 'user_id', '_id')->select('full_name');
    }

     public function ModifiedBy()
    {
        return $this->belongsTo('App\Models\User', 'action_by', '_id')->select('full_name');
    }
}
