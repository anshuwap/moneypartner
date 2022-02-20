<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Api\OfflinePayoutApi;
use App\Models\Webhook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebhookApiController extends Controller
{

    public function index()
    {
        try {
            $data['webhook'] = Webhook::where('retailer_id', Auth::user()->_id)->first();
            return view('retailer.webhook_api.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function store(Request $request)
    {

        try {
            $webhook = new Webhook();
            if (!empty($request->webhook_id))
                $webhook = Webhook::find($request->webhook_id);

            $webhook->retailer_id = Auth::user()->_id;
            $webhook->webhook_url = $request->webhook_url;
            if (!$webhook->save())
                return response(['status' => 'error', 'msg' => 'Webhook URL not integrated!']);

            return response(['status' => 'success', 'msg' => 'Webhook URL integrated!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
