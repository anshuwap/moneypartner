<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data['filter']  = $request->all();
            $data['setting'] = Setting::first();
            return view('admin.setting', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {

            $setting = Setting::first();

            $res = false;
            if (empty($setting)) {
                $settingC = new Setting();
                $settingC->status = (int)$request->status;
                $settingC->comment = $request->comment;
                if ($settingC->save())
                    $res = true;
            } else {
                $setting = Setting::find($setting->_id);
                $setting->status = (int)$request->status;
                $setting->comment = $request->comment;
                if ($setting->save())
                    $res = true;
            }

            if ($res)
                return response(['status' => 'success', 'msg' => 'Maintanance Status Updated Successfully!']);

            return response(['status' => 'error', 'msg' => 'Something went wrong!']);
        } catch (Exception $e) {
        }
    }
}
