<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class MaintainanceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function maintanance()
    {
        return view('admin.500');
    }
}
