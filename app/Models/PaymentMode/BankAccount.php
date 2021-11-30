<?php

namespace App\Models\PaymentMode;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BankAccount extends BaseModel
{
    use HasFactory;

    public function scopeAllCount($query){

        return $query->count();
        }

        public function scopeLikeColumn($query,$val){

            $query->where('bank_name', 'like', "%$val%");
            $query->orWhere('account_number', 'like', "%$val%");
            $query->orWhere('ifsc', 'like', "%$val%");
            $query->orWhere('account_holder_name', 'like', "%$val%");
            return $query->count();
        }

        public function scopeGetResult($query,$val){

            $query->where('bank_name', 'like', "%$val%");
            $query->orWhere('account_number', 'like', "%$val%");
            $query->orWhere('ifsc', 'like', "%$val%");
            $query->orWhere('account_holder_name', 'like', "%$val%");
            return $query->get();
        }
}
