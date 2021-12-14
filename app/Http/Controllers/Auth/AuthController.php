<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;

use Hash;
//use Cache;



class AuthController extends Controller
{


    public function admin()
    {
        return view('auth.admin');
    }

    public function getRegister()
    {
        return view('auth.register');
    }



    public function postRegister(Request $request)
    {
        $this->validate($request, [
            'email'=>'required|email|unique:users|min:8|max:30',
            'name'=>'required|unique:users|min:8|max:20',
            'password'=>'required|confirmed|min:6',
        ]);


       $user = new User();
       $user->name = $request['name'];
       $user->email=$request['email'];
       $user->password=Hash::make($request['password']);
       $user->save();

       Auth::login($user);

       return redirect()->route('admin');

    }






    public function getLogout()
    {

        Auth::logout();

        return redirect()->route('getLogin');

    }


    public function getLogin()
    {
        return view('auth.login');
    }


    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'=>'required|email',
            'password'=>'required|min:6',
        ]);


        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // Аутентификация успешна
            return redirect('auth/admin');
        }

        return redirect()->back();
    }
    
}
