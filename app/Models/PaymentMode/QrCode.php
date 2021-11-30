<?php

namespace App\Models\PaymentMode;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QrCode extends BaseModel
{
    use HasFactory;

    public function scopeAllCount($query){

        return $query->count();
        }

        public function scopeLikeColumn($query,$val){

            $query->where('name', 'like', "%$val%");
            return $query->count();
        }

        public function scopeGetResult($query,$val){

            $query->where('name', 'like', "%$val%");
            return $query->get();
        }
}
