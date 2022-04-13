<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Http\Validation\EcollectionValidation;
use App\Models\Api\ECollection;
use App\Models\Outlet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EcollectionController extends Controller
{

    public function index(Request $request)
    {
        try {


            $query = ECollection::query();

            $query->where('outlet_id', Auth::user()->outlet_id);

            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            if (!empty($start_date) && !empty($end_date)) {
                $start_date = strtotime(trim($start_date) . " 00:00:00");
                $end_date   = strtotime(trim($end_date) . " 23:59:59");
            } else {
                $start_date = strtotime(trim(date('d-m-Y') . " 00:00:00"));
                $end_date = strtotime(trim(date('Y-m-d') . " 23:59:59"));
            }

            $query->whereBetween('created', [$start_date, $end_date]);

            $perPage = (!empty($request->perPage)) ? $request->perPage : config('constants.perPage');
            $data['ecollections'] = $query->paginate($perPage);
            $request->request->remove('page');
            $request->request->remove('perPage');
            $data['filter']  = $request->all();
            return view('retailer.e_collection.display', $data);
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }
}
