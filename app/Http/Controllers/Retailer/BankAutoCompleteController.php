<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAutoCompleteController extends Controller
{

    public function fetch(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = Transaction::where('outlet_id', Auth::user()->outlet_id)->where('receiver_name', 'LIKE', "%{$query}%")->get();
            $output = '<ul class="dropdown-menu px-2 auto-field">';
            foreach ($data->unique('payment_channel.bank_name') as $row) {
                $output .= '
                    <li class="custom1 custom-auto-field" style=""><a style="color:black !important" href="javascript:void(0)" data-bank="' . $row->payment_channel['bank_name'] . '" data-account="' . $row->payment_channel['account_number'] . '" data-ifsc="' . $row->payment_channel['ifsc_code'] . '" data-name="' . $row->receiver_name . '">' . $row->receiver_name . ' - ' . $row->payment_channel['bank_name'] . '</a></li>
                ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function fetchAccount(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = Transaction::where('outlet_id', Auth::user()->outlet_id)->where('payment_channel.account_number', 'LIKE', "%{$query}%")->get();
            $output = '<ul class="dropdown-menu px-2 auto-field" >';
            foreach ($data->unique('payment_channel.account_number') as $row) {
                $output .= '
                    <li class="custom custom-auto-field"><a style="color:black !important" href="javascript:void(0)">' . $row->payment_channel['account_number'] . '</a></li>
                ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function fetchIfsc(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = Transaction::where('outlet_id', Auth::user()->outlet_id)->where('payment_channel.ifsc_code', 'LIKE', "%{$query}%")->get();
            $output = '<ul class="dropdown-menu px-2 auto-field">';
            foreach ($data->unique('payment_channel.ifsc_code') as $row) {
                $output .= '
                    <li class="custom2 custom-auto-field"><a style="color:black !important" href="javascript:void(0)">' . $row->payment_channel['ifsc_code'] . '</a></li>
                ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
}
