<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(){ return view('auth.login'); }
    public function login(Request $request){
        $credentials=$request->validate(['login'=>'required','password'=>'required']);
        $login=$credentials['login'];
        $field=filter_var($login,FILTER_VALIDATE_EMAIL)?'email':'username';
        if(Auth::attempt([$field=>$login,'password'=>$credentials['password']],$request->boolean('remember'))){
            $request->session()->regenerate();
            $user=Auth::user();
            if($user->status!== 'aktif'){ Auth::logout(); return back()->withErrors(['login'=>'Akun tidak aktif.']); }
            return redirect()->intended($user->role==='admin'?'/admin/dashboard':'/nasabah/dashboard');
        }
        return back()->withErrors(['login'=>'Email/username atau password salah.']);
    }
    public function showRegister(){ return view('auth.register'); }
    public function register(Request $request){
        $data=$request->validate(['name'=>'required|max:150','username'=>'required|alpha_dash|max:50|unique:users','email'=>'required|email|max:150|unique:users','phone'=>'required|max:20','password'=>'required|min:8|confirmed']);
        $data['role']='nasabah'; $data['status']='aktif'; $data['balance']=0; $data['password']=Hash::make($data['password']);
        $user=User::create($data); Auth::login($user); $request->session()->regenerate();
        return redirect('/nasabah/dashboard')->with('success','Registrasi berhasil.');
    }
    public function logout(Request $request){ Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect('/login'); }
}
