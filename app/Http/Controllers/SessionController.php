<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionController extends Controller
{
    public function __construct()
    {
        //只让未登录用户访问登录页面
        //使用 Auth 中间件提供的 guest 选项，用于指定一些只允许未登录用户访问的动作
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    public function create()
    {
    	return view('sessions.create');
    }

    public function store(Request $request)
    {	
    	$this->validate($request,[
    		'email' => 'required|email|max:255',
    		'password' => 'required',
    	]);

    	$credentials = [
    		'email' => $request->email,
    		'password' => $request->password,
    	];

    	if (Auth::attempt($credentials,$request->has('remember'))) {
    		session()->flash('success','欢迎回来!');
    		return redirect()->intended(route('users.show',[Auth::user()]));
    	}else{
    		session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
    		return redirect()->back();
    	}

    }

    public function destroy()
    {
    	Auth::logout();
    	session()->flash('success','你已成功退出');
    	return redirect()->route('login');
    }
}
