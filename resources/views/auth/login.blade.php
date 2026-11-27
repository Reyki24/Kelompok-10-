@extends('layouts.auth')
@section('title','Login - RecycleHub')
@section('content')
<section class="auth-visual"><a class="auth-brand"><i class="bi bi-recycle"></i><strong>RecycleHub</strong></a><h2>Sistem Informasi<br>Bank Sampah</h2><p>Kelola setoran dan tabungan sampah dengan mudah.</p><div class="bins"><i class="bi bi-bottle"></i><i class="bi bi-box-seam"></i><i class="bi bi-can"></i></div></section>
<section class="auth-card-wrap"><form class="auth-card" method="post" action="/login">@csrf<h1>Masuk</h1><p>Silakan masuk ke akun Anda</p>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<label>Email atau Username</label><div class="input-wrap"><i class="bi bi-person"></i><input name="login" value="{{ old('login') }}" required autofocus></div>
<label>Password</label><div class="input-wrap"><i class="bi bi-lock"></i><input type="password" name="password" id="password" required><button type="button" class="password-toggle" data-target="password"><i class="bi bi-eye"></i></button></div>
<div class="auth-options"><label><input type="checkbox" name="remember"> Ingat saya</label></div>
<button class="btn-auth">Masuk</button><div class="auth-link">Belum punya akun? <a href="/register">Daftar sekarang</a></div></form></section>
@endsection
