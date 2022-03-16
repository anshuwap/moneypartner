<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionComment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionCommentController extends Controller
{

    public function index()
    {
        try {
            return view('admin.transaction_comment.list');
        } catch (Exception $e) {
            return redirect('500')->with(['error' => $e->getMessage()]);;
        }
    }


    public function create()
    {
    }


    public function store(Request $request)
    {
        $transactionComment = new TransactionComment();
        $transactionComment->user_id      = Auth::user()->_id;
        $transactionComment->type         = $request->type;
        $transactionComment->comment      = $request->comment;
        $transactionComment->status       = $request->status;

        if ($transactionComment->save())
            return response(['status' => 'success', 'msg' => 'Transaction Comment Added Successfully!']);

        return response(['status' => 'error', 'msg' => 'Transaction Comment not Added Successfully!']);
    }





    public function edit(TransactionComment $TransactionComment,$id)
    {
        try {
            $data = TransactionComment::find($id);
            die(json_encode($data));
        } catch (Exception $e) {
            return redirect('500');
        }
    }


    public function update(Request $request,$id)
    {

        $transactionComment = TransactionComment::find($id);
        $transactionComment->type         = $request->type;
        $transactionComment->comment      = $request->comment;
        $transactionComment->status       = $request->status;

        if ($transactionComment->save())
            return response(['status' => 'success', 'msg' => 'Transaction Comment Updated Successfully!']);

        return response(['status' => 'error', 'msg' => 'Transaction Comment not Updated Successfully!']);
    }


    public function destroy($id)
    {
        try {
            $res = TransactionComment::where('_id', $id)->delete();
            if ($res)
                return response(['status' => 'success', 'msg' => 'Transaction Comment Removed Successfully!']);

            return response(['status' => 'error', 'msg' => 'Transaction Comment not Removed!']);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function commentStatus(Request $request)
    {

        try {
            $transactionComment = TransactionComment::find($request->id);
            $transactionComment->status = (int)$request->status;
            $transactionComment->save();
            if ($transactionComment->status == 1)
                return response(['status' => 'success', 'msg' => 'This Transaction Comment is Active!', 'val' => $transactionComment->status]);

            return response(['status' => 'success', 'msg' => 'This Transaction Comment is Inactive!', 'val' => $transactionComment->status]);
        } catch (Exception $e) {
            return response(['status' => 'error', 'msg' => 'Something went wrong!!']);
        }
    }


    public function ajaxList(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];

        // count all data
        $totalRecords = TransactionComment::AllCount();

        if (!empty($searchValue)) {
            // count all data
            $totalRecordswithFilter = TransactionComment::LikeColumn($searchValue);
            $data = TransactionComment::GetResult($searchValue);
        } else {
            // get per page data
            $totalRecordswithFilter = $totalRecords;
            $data = TransactionComment::offset($start)->limit($length)->orderBy('created', 'DESC')->get();
        }
        $dataArr = [];
        $i = 1;

        foreach ($data as $val) {
            $action = '<a href="javascript:void(0);" class="text-info edit_comment" data-toggle="tooltip" data-placement="bottom" title="Edit" comment_id="' . $val->_id . '"><i class="far fa-edit"></i></a>&nbsp;&nbsp;';
           // $action .= '<a href="javascript:void(0);" class="text-danger remove_comment"  data-toggle="tooltip" data-placement="bottom" title="Remove" comment_id="' . $val->_id . '"><i class="fas fa-trash"></i></a>';
            if ($val->status == 1) {
                $status = ' <a href="javascript:void(0);"><span class="badge badge-success activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="0">Active</span></a>';
            } else {
                $status = ' <a href="javascript:void(0)"><span class="badge badge-danger activeVer" id="active_' . $val->_id . '" _id="' . $val->_id . '" val="1">Inactive</span></a>';
            }
            $dataArr[] = [
                'sl_no'             => $i,
                'type'              => $val->type,
                'comment'           => ucfirst($val->comment),
                'created_date'      => date('Y-m-d', $val->created),
                'status'            => $status,
                'action'            => $action
            ];
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" =>  $totalRecordswithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $dataArr
        );
        echo json_encode($response);
        exit;
    }
}
