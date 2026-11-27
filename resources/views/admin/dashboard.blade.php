@extends('layouts.app')@section('title','Dashboard Admin')@section('header','Dashboard')
@section('content')
<div class="page-head"><h2>Ringkasan Bank Sampah</h2></div>
<div class="stat-grid">
<div class="stat-card"><div class="stat-icon"><i class="bi bi-people"></i></div><div><span>Total Nasabah</span><strong>{{ number_format($totalNasabah) }}</strong></div></div>
<div class="stat-card"><div class="stat-icon"><i class="bi bi-arrow-down-left-circle"></i></div><div><span>Total Setoran</span><strong>{{ number_format($totalSetoran) }}</strong></div></div>
<div class="stat-card"><div class="stat-icon"><i class="bi bi-wallet2"></i></div><div><span>Pengajuan Penarikan</span><strong>{{ number_format($pending) }}</strong></div></div>
<div class="stat-card"><div class="stat-icon"><i class="bi bi-cash-stack"></i></div><div><span>Total Saldo Nasabah</span><strong>Rp {{ number_format($totalSaldo,0,',','.') }}</strong></div></div>
</div>
<div class="row g-3"><div class="col-lg-8"><div class="card-box chart-box"><h5>Nilai Setoran Bulanan</h5><canvas id="depositChart"></canvas></div></div><div class="col-lg-4"><div class="card-box p-3"><h5>Transaksi Terbaru</h5><div class="table-wrap"><table class="table"><tbody>@foreach($recent as $d)<tr><td><strong>{{ $d->user->name }}</strong><br><small class="text-secondary">{{ $d->wasteType->name }}</small></td><td class="text-end"><strong>Rp {{ number_format($d->total_amount,0,',','.') }}</strong><br><small>{{ $d->created_at->diffForHumans() }}</small></td></tr>@endforeach</tbody></table></div></div></div></div>
@endsection
@section('scripts')<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>new Chart(document.getElementById('depositChart'),{type:'line',data:{labels:@json($labels),datasets:[{label:'Nilai Setoran',data:@json($chartData),borderColor:'#15803d',backgroundColor:'rgba(34,197,94,.12)',fill:true,tension:.35}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}})</script>@endsection
