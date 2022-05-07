<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\PaymentMode\BankAccount;
use App\Models\PaymentMode\QrCode;
use App\Models\PaymentMode\Upi;
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

    public function paymentModeName($type, $id)
    {
        switch ($type) {
            case 'bank_account':
                $value = BankAccount::select('bank_name')->find($id);
                $name = '-';
                if (!empty($value))
                    $name = $value->bank_name;
                break;
            case 'upi_id':
                $value = Upi::select('upi_id')->find($id);
                $name = '-';
                if (!empty($value))
                    $name = $value->upi_id;
                break;
            case 'qr_code':
                $value = QrCode::select('name')->find($id);
                if (!empty($value))
                    $name = $value->name;
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    public function RetailerName()
    {
        return $this->belongsTo('App\Models\User', 'retailer_id', '_id')->select('outlet_name');
    }

    public function UserName()
    {
        return $this->belongsTo('App\Models\User', 'action_by', '_id')->select('full_name');
    }
}
