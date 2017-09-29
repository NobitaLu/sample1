<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth',[
			'except' => ['show','store','create','index']//除了这些都过滤
		]);

		//只让未登录用户访问注册页面
		//使用 Auth 中间件提供的 guest 选项，用于指定一些只允许未登录用户访问的动作
		$this->middleware('guest',[
			'only' => ['create']//只过滤这一个
		]);
	}

    public function create()
    {
    	return view('users.create');
    }

    public function show(User $user)
    {
    	return view('users.show',compact('user'));
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
    		'name' => 'required|max:50',
    		'email' => 'required|email|unique:users|max:255',
    		'password' => 'required|confirmed'
    	]);

    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => bcrypt($request->password),
    	]);
    	Auth::login($user);
    	session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
    	return redirect()->route('users.show',[$user]);
    }

    public function edit(User $user)
    {
    	$this->authorize('update',$user);
    	return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request)
    {
    	$this->validate($request,[
    		'name' => 'required|max:50',
    		'password' => 'nullable|confirmed|min:6',
    	]);

    	$this->authorize('update',$user);

    	$data = [];
    	$data['name'] = $request->name;
    	if ($request->password) {
    		$data['password'] = bcrypt($request->password);
    	}
    	$user->update($data);

    	session()->flash('success','个人资料更新成功!');
    	return redirect()->route('users.show',$user->id);
    }

    public function index()
    {
    	$users = User::paginate(10);
    	return view('users.index',compact('users'));
    }

    public function destroy(User $user)
    {
    	$this->authorize('destroy',$user);
    	$user->delete();
    	session()->flash('success','成功删除用户!');
    	return back();
    }
}
