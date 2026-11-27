@extends('layouts.app')@section('title','Informasi Harga Sampah')@section('header','Harga Sampah')
@section('content')
<div class="page-head"><h2>Informasi Harga Sampah</h2></div><div class="waste-grid">@foreach($wastes as $w)<div class="card-box waste-card"><i class="bi bi-box-seam"></i><h3>{{ $w->name }}</h3><strong class="text-success">Rp {{ number_format($w->price_per_kg,0,',','.') }}/kg</strong><p class="text-secondary mt-2">{{ $w->description ?? 'Jenis sampah daur ulang yang diterima RecycleHub.' }}</p></div>@endforeach</div>
@endsection
