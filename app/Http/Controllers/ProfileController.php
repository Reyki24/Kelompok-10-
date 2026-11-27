<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $r){ return view('nasabah.profile',['user'=>$r->user()]); }
    public function update(Request $r){
        $user=$r->user();
        $data=$r->validate(['name'=>'required|max:150','username'=>'required|max:50|alpha_dash|unique:users,username,'.$user->id,'email'=>'required|email|max:150|unique:users,email,'.$user->id,'phone'=>'required|max:20','password'=>'nullable|min:8|confirmed','profile_photo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048']);
        if(!empty($data['password'])) $data['password']=Hash::make($data['password']); else unset($data['password']);
        if($r->hasFile('profile_photo')){ if($user->profile_photo) Storage::disk('public')->delete($user->profile_photo); $data['profile_photo']=$r->file('profile_photo')->store('profiles','public'); }
        unset($data['profile_photo_confirmation']);
        $user->update($data); return back()->with('success','Profil berhasil diperbarui.');
    }
}
