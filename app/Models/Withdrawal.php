<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends BaseModel
{
    use HasFactory;

    public function EmployeeName()
    {
        return $this->belongsTo('App\Models\User', 'employee_id', '_id')->select('full_name');
    }


    public function UserName()
    {
        return $this->belongsTo('App\Models\User', 'action_by', '_id')->select('full_name');
    }
}
