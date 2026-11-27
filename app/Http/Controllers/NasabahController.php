<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Deposit,WithdrawalRequest,WasteType};
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    public function dashboard(Request $r){
        $user=$r->user();
        $deposits=Deposit::where('user_id',$user->id)->where('status','berhasil');
        return view('nasabah.dashboard',['user'=>$user,'totalSetoran'=>(clone $deposits)->count(),'totalBerat'=>(float)(clone $deposits)->sum('weight'),'totalNilai'=>(float)(clone $deposits)->sum('total_amount'),'recent'=>(clone $deposits)->with('wasteType')->latest()->take(5)->get()]);
    }
    public function wastes(){ return view('nasabah.wastes',['wastes'=>WasteType::where('status','aktif')->get()]); }
    public function deposits(Request $r){
        $query=Deposit::with('wasteType')->where('user_id',$r->user()->id)->when($r->search,fn($q,$s)=>$q->whereHas('wasteType',fn($w)=>$w->where('name','like',"%$s%")))->when($r->date,fn($q,$d)=>$q->whereDate('transaction_date',$d))->latest();
        $deposits=$query->paginate(10)->withQueryString();
        $summary=['weight'=>(float)Deposit::where('user_id',$r->user()->id)->where('status','berhasil')->sum('weight'),'value'=>(float)Deposit::where('user_id',$r->user()->id)->where('status','berhasil')->sum('total_amount')];
        return view('nasabah.deposits',compact('deposits','summary'));
    }
    public function withdrawal(Request $r){
        $requests=WithdrawalRequest::where('user_id',$r->user()->id)->latest()->paginate(10);
        return view('nasabah.withdrawal',['requests'=>$requests,'user'=>$r->user()]);
    }
    public function storeWithdrawal(Request $r){
        $data=$r->validate(['amount'=>'required|numeric|min:100','notes'=>'nullable|max:1000']);
        $user=$r->user();
        if((float)$data['amount']>(float)$user->balance) return back()->withErrors(['amount'=>'Jumlah penarikan melebihi saldo tersedia.']);
        WithdrawalRequest::create(['request_code'=>'REQ-'.now()->format('YmdHis').'-'.rand(100,999),'user_id'=>$user->id,'amount'=>$data['amount'],'request_date'=>now(),'status'=>'menunggu','notes'=>$data['notes']??null]);
        return back()->with('success','Pengajuan penarikan berhasil dikirim.');
    }
}
