<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerTrans extends BaseModel
{
    use HasFactory;


    public function scopeAllCount($query){

    return $query->count();
    }

    public function scopeLikeColumn($query,$val){

        $query->where('sender_name', 'like', "%$val%");
        $query->orWhere('mobile_number', 'like', "%$val%");
        $query->orWhere('amount', 'like', "%$val%");
        $query->orWhere('receiver_name', 'like', "%$val%");
        $query->orWhere('payment_mode', 'like', "%$val%");
        return $query->count();
    }

    public function scopeGetResult($query,$val){

        $query->where('sender_name', 'like', "%$val%");
        $query->orWhere('mobile_number', 'like', "%$val%");
        $query->orWhere('amount', 'like', "%$val%");
        $query->orWhere('receiver_name', 'like', "%$val%");
        $query->orWhere('payment_mode', 'like', "%$val%");
        return $query->get();
    }
}
