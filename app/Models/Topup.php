<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topup extends BaseModel
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


        public function RetailerName(){

            return $this->belongsTo('App\Models\User', 'retailer_id', '_id')->select('full_name');
        }
}
