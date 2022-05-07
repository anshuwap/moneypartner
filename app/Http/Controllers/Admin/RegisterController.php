<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

  public function index()
  {
    try{
    return view('admin.register');
    }catch(Exception $e){
      return redirect('500');
    }
  }



  public function store(Request $request)
  {

    try{
   

    $data = $request->all();
    $check = $this->create($data);

    // return redirect()->back()->with('message', 'Register Successfully, Please Sign In!');
    return redirect('admin')->with('success', 'Register Successfully, Please Sign In!');
  } catch (Exception $e) {
    return redirect('500');
}
  }



  public function create(array $data)
  {
    return User::create([
      'full_name' => $data['full_name'],
      'email'     => $data['email'],
      'role'      =>  'admin',
      'password'  => Hash::make($data['password'])
    ]);
  }
}
