<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','RecycleHub')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="{{ asset('css/recyclehub.css') }}" rel="stylesheet">
</head>
<body>
<aside class="sidebar" id="sidebar">
  <a class="brand" href="#"><span class="brand-mark"><i class="bi bi-recycle"></i></span><span><strong>Recycle</strong>Hub</span></a>
  @if(auth()->user()->role==='admin')
  <nav>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard')?'active':'' }}"><i class="bi bi-grid"></i>Dashboard</a>
    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users')?'active':'' }}"><i class="bi bi-people"></i>Data Nasabah</a>
    <a href="{{ route('admin.wastes') }}" class="{{ request()->routeIs('admin.wastes')?'active':'' }}"><i class="bi bi-trash3"></i>Jenis &amp; Harga</a>
    <a href="{{ route('admin.deposits') }}" class="{{ request()->routeIs('admin.deposits')?'active':'' }}"><i class="bi bi-arrow-down-left-circle"></i>Transaksi Setoran</a>
    <a href="{{ route('admin.withdrawals') }}" class="{{ request()->routeIs('admin.withdrawals')?'active':'' }}"><i class="bi bi-wallet2"></i>Penarikan Saldo</a>
    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports')?'active':'' }}"><i class="bi bi-bar-chart"></i>Laporan</a>
  </nav>
  @else
  <nav>
    <a href="{{ route('nasabah.dashboard') }}" class="{{ request()->routeIs('nasabah.dashboard')?'active':'' }}"><i class="bi bi-grid"></i>Dashboard</a>
    <a href="{{ route('nasabah.wastes') }}" class="{{ request()->routeIs('nasabah.wastes')?'active':'' }}"><i class="bi bi-tags"></i>Harga Sampah</a>
    <a href="{{ route('nasabah.deposits') }}" class="{{ request()->routeIs('nasabah.deposits')?'active':'' }}"><i class="bi bi-clock-history"></i>Riwayat Setoran</a>
    <a href="{{ route('nasabah.withdrawal') }}" class="{{ request()->routeIs('nasabah.withdrawal')?'active':'' }}"><i class="bi bi-wallet2"></i>Penarikan Saldo</a>
    <a href="{{ route('nasabah.profile') }}" class="{{ request()->routeIs('nasabah.profile')?'active':'' }}"><i class="bi bi-person"></i>Profil</a>
  </nav>
  @endif
  <form action="{{ route('logout') }}" method="post" class="logout">@csrf<button><i class="bi bi-box-arrow-right"></i>Logout</button></form>
</aside>
<div class="main-shell">
  <header class="topbar">
    <button class="menu-toggle" id="menuToggle"><i class="bi bi-list"></i></button>
    <div><small>RecycleHub</small><h1>@yield('header')</h1></div>
    <div class="profile-chip"><span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div></div>
  </header>
  <main class="content">@yield('content')</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/recyclehub.js') }}"></script>
@if(session('success'))<script>Swal.fire({toast:true,position:'top-end',icon:'success',title:@json(session('success')),showConfirmButton:false,timer:3000})</script>@endif
@yield('scripts')
</body></html>
