<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    public function LoginController(Request $request)
    {
        if($request->username == null || $request->password == null)
        {
            return redirect()->back()->with('error', 'Username dan Password harus dimasukan.');
        }
        else
        {
            $userCount = DB::TABLE("kurir")->where("name", $request->username)->where("password", md5($request->password))->count();
            if($userCount == 0)
            {
                return redirect()->back()->with('error', 'Username tidak terdaftar.');
            }
            else
            {
                session()->put('username', $request->username);
                return redirect('/');
            }
        }
    }
}
