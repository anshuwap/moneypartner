<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Validation\EcollectionValidation;
use App\Http\Validation\OfflineBulkPayoutValidation;
use App\Http\Validation\OfflinePayoutValidation;
use App\Models\Api\ECollection;
use App\Models\ApiList;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Support\PaymentApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EcollectionController extends Controller
{

  //for single payout system
  public function eCollection(EcollectionValidation $request)
  {
    try {

      $txn = uniqCode(3) . rand(111111, 999999);

      /*start api transfer functionality*/
      $eCollection = new ECollection();
      $eCollection->outlet_id       = Auth::user()->outlet_id;
      $eCollection->transaction_id  = $txn;
      $eCollection->date            = time();
      $eCollection->outlet_name     = Auth::user()->outlet_name;
      $eCollection->payer_name      = $request->payer_name;
      $eCollection->amount          = $request->amount;
      $eCollection->status          = 'Pending';

      if (!$eCollection->save())
        return response(['status' => FALSE, 'flag' => 'collection_not_created', 'msg' => 'Something went wrong!']);

      // return response(['status' => TRUE, 'flag' => 'collection_created', 'msg' => 'Success']);
      $amount = $request->amount;
      $data['key'] = '15';
      $temp = '966A0C323CC8225CE79EA3DF11B78192|' . $amount . '|15|' . $txn;
      $hash = md5($temp);
      //return view('test-payment', $data);
      return response(['status' => TRUE, 'flag' => 'collection_created', 'msg' => 'Success', 'url' => url('/eCollectionProcess/'.$amount . '/' . $txn . '/15/' . $hash)]);
    } catch (Exception $e) {
      return response(['status' => FALSE, 'flag' => 'system_error', 'msg' => $e->getMessage()]);
    }
  }


  public function eCollectionTemp($outlet_name, $amount, $payer_namem)
  {
    try {

      $txn = uniqCode(3) . rand(111111, 999999);

      $outlet = Outlet::where('outlet_name', $outlet_name)->first();

      if (!empty($outlet->money_transfer_option['e_collection']) && $outlet->money_transfer_option['e_collection'] == 1){
      }else{
        return response(['status' => FALSE, 'flag' => 'service_not_allowed', 'msg' => 'E-Collection Service not enable for you!']);
      }

      /*start api transfer functionality*/
      $eCollection = new ECollection();
      $eCollection->outlet_id       = $outlet->id;
      $eCollection->transaction_id  = $txn;
      $eCollection->date            = time();
      $eCollection->outlet_name     = $outlet->outlet_name;
      $eCollection->payer_name      = $payer_name;
      $eCollection->amount          = $amount;
      $eCollection->status          = 'pending';

      if (!$eCollection->save())
        return response(['status' => FALSE, 'flag' => 'collection_not_created', 'msg' => 'Something went wrong!']);

      // return response(['status' => TRUE, 'flag' => 'collection_created', 'msg' => 'Success']);
      $data['amount'] = $amount;
      $data['transaction_id'] = $txn;
      $data['key'] = '15';
      $temp = '966A0C323CC8225CE79EA3DF11B78192' . '|' . $amount . '|15|' . $txn;
      $data['hash'] = md5($temp);
      return view('test-payment', $data);
    } catch (Exception $e) {
      return response(['status' => FALSE, 'flag' => 'system_error', 'msg' => $e->getMessage()]);
    }
  }


  public function eCollectionNew($amount, $txn, $id, $hash)
  {
    try {
	  $data['key'] = $id;
      $data['txn'] = $txn;
      $data['amount'] = $amount;
      $data['hash'] = $hash;
      return view('test-payment', $data);
    } catch (Exception $e) {
      return response(['status' => FALSE, 'flag' => 'system_error', 'msg' => $e->getMessage()]);
    }
  }



  public function webhookUrl(Request $request)
  {
    $eCollection = ECollection::where('transaction_id', $request->merchantRefNo)->first();
    //$eCollection->webhook_url = $request->all();
    $eCollection->status = $request->status;
    $eCollection->save();
  }
}
