@extends('layouts.auth')
@section('title','Daftar - RecycleHub')
@section('content')
<section class="auth-visual"><a class="auth-brand"><i class="bi bi-recycle"></i><strong>RecycleHub</strong></a><h2>Gabung Sebagai<br>Nasabah</h2><p>Mulai kelola sampah dan tabungan Anda.</p></section>
<section class="auth-card-wrap"><form class="auth-card register-card" method="post" action="/register">@csrf<h1>Daftar Nasabah</h1>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="row g-3"><div class="col-md-6"><label>Nama Lengkap</label><input class="form-control" name="name" value="{{ old('name') }}" required></div><div class="col-md-6"><label>Username</label><input class="form-control" name="username" value="{{ old('username') }}" required></div><div class="col-md-6"><label>Email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div><div class="col-md-6"><label>No. HP</label><input class="form-control" name="phone" value="{{ old('phone') }}" required></div><div class="col-md-6"><label>Password</label><input class="form-control" type="password" name="password" required></div><div class="col-md-6"><label>Konfirmasi Password</label><input class="form-control" type="password" name="password_confirmation" required></div></div>
<button class="btn-auth mt-3">Daftar</button><div class="auth-link">Sudah punya akun? <a href="/login">Masuk</a></div></form></section>
@endsection
