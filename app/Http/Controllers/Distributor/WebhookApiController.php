<?php

namespace App\Http\Controllers\Distributor;

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
            $data['webhook'] = Webhook::where('retailer_id', Auth::user()->_id)->where('type','webhook')->first();
            $data['base_url'] = Webhook::where('retailer_id', Auth::user()->_id)->where('type','base_url')->first();
            return view('distributor.setting.webhook', $data);
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
            $webhook->type        = 'webhook';
            if (!$webhook->save())
                return response(['status' => 'error', 'msg' => 'Webhook URL not integrated!']);

            return response(['status' => 'success', 'msg' => 'Webhook URL integrated!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }


        public function baseUrlApi(Request $request)
    {
        try {
            $base_url = new Webhook();
            if (!empty($request->base_url_id))
                $base_url = Webhook::find($request->base_url_id);

            $base_url->retailer_id = Auth::user()->_id;
            $base_url->base_url = $request->base_url;
            $base_url->type        = 'base_url';
            if (!$base_url->save())
                return response(['status' => 'error', 'msg' => 'Base URL not integrated!']);

            return response(['status' => 'success', 'msg' => 'Base URL integrated!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
