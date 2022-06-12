<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeCommission extends BaseModel
{
    use HasFactory;

    public function Transaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'transaction_id', '_id')->select('transaction_id');
    }

    public function OutletName()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id', '_id')->select('outlet_name');
    }

    public function ActionBy()
    {
        return $this->belongsTo('App\Models\User', 'action_by', '_id')->select('full_name');
    }

     public function EmpName()
    {
        return $this->belongsTo('App\Models\User', 'employee_id', '_id')->select('full_name');
    }
}
