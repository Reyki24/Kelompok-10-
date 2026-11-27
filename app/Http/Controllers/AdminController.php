<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User,WasteType,Deposit,WithdrawalRequest,Withdrawal};
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(){
        $monthly=Deposit::selectRaw('MONTH(transaction_date) month, SUM(total_amount) total')->whereYear('transaction_date',now()->year)->where('status','berhasil')->groupBy('month')->pluck('total','month');
        $labels=[];$data=[];for($m=1;$m<=12;$m++){ $labels[]=now()->month($m)->locale('id')->shortMonthName; $data[]=(float)($monthly[$m]??0); }
        return view('admin.dashboard',['totalNasabah'=>User::where('role','nasabah')->whereNull('deleted_at')->count(),'totalSetoran'=>Deposit::where('status','berhasil')->count(),'pending'=>WithdrawalRequest::where('status','menunggu')->count(),'totalSaldo'=>User::where('role','nasabah')->sum('balance'),'recent'=>Deposit::with(['user','wasteType'])->latest()->take(5)->get(),'labels'=>$labels,'chartData'=>$data]);
    }
    public function users(Request $r){
        $users=User::withTrashed()->where('role','nasabah')->when($r->search,fn($q,$s)=>$q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('username','like',"%$s%")->orWhere('email','like',"%$s%")->orWhere('phone','like',"%$s%")))->when($r->status,fn($q,$s)=>$q->where('status',$s))->latest()->paginate(10)->withQueryString();
        return view('admin.users',compact('users'));
    }
    public function storeUser(Request $r){
        $data=$r->validate(['name'=>'required|max:150','username'=>'required|max:50|alpha_dash|unique:users','email'=>'required|email|max:150|unique:users','phone'=>'nullable|max:20','password'=>'required|min:8','status'=>'required|in:aktif,nonaktif']);
        $data['role']='nasabah'; $data['balance']=0; $data['password']=bcrypt($data['password']);
        User::create($data); return back()->with('success','Nasabah berhasil ditambahkan.');
    }
    public function updateUser(Request $r,User $user){
        $data=$r->validate(['name'=>'required|max:150','username'=>'required|max:50|alpha_dash|unique:users,username,'.$user->id,'email'=>'required|email|max:150|unique:users,email,'.$user->id,'phone'=>'nullable|max:20','status'=>'required|in:aktif,nonaktif','password'=>'nullable|min:8']);
        if(!empty($data['password'])) $data['password']=bcrypt($data['password']); else unset($data['password']);
        $user->update($data); return back()->with('success','Nasabah berhasil diperbarui.');
    }
    public function deleteUser(User $user){ $user->delete(); return back()->with('success','Nasabah berhasil dinonaktifkan dan dihapus.'); }
    public function wastes(Request $r){ $wastes=WasteType::withTrashed()->when($r->search,fn($q,$s)=>$q->where('name','like',"%$s%"))->latest()->paginate(10)->withQueryString(); return view('admin.wastes',compact('wastes')); }
    public function storeWaste(Request $r){ $data=$r->validate(['name'=>'required|max:100|unique:waste_types','price_per_kg'=>'required|numeric|min:0','description'=>'nullable','status'=>'required|in:aktif,nonaktif']); WasteType::create($data); return back()->with('success','Jenis sampah berhasil ditambahkan.'); }
    public function updateWaste(Request $r,WasteType $waste){ $data=$r->validate(['name'=>'required|max:100|unique:waste_types,name,'.$waste->id,'price_per_kg'=>'required|numeric|min:0','description'=>'nullable','status'=>'required|in:aktif,nonaktif']); $waste->update($data); return back()->with('success','Jenis sampah berhasil diperbarui.'); }
    public function deleteWaste(WasteType $waste){ $waste->update(['status'=>'nonaktif']); $waste->delete(); return back()->with('success','Jenis sampah dinonaktifkan.'); }
    public function deposits(Request $r){
        $deposits=Deposit::with(['user','wasteType'])->when($r->search,fn($q,$s)=>$q->whereHas('user',fn($u)=>$u->where('name','like',"%$s%")))->when($r->date,fn($q,$d)=>$q->whereDate('transaction_date',$d))->latest()->paginate(10)->withQueryString();
        $users=User::where('role','nasabah')->whereNull('deleted_at')->get(); $wastes=WasteType::where('status','aktif')->get();
        return view('admin.deposits',compact('deposits','users','wastes'));
    }
    public function storeDeposit(Request $r,BalanceService $balance){
        $data=$r->validate(['user_id'=>'required|exists:users,id','transaction_date'=>'required|date','waste_type_id'=>'required|exists:waste_types,id','weight'=>'required|numeric|min:0.01','notes'=>'nullable']);
        $waste=WasteType::findOrFail($data['waste_type_id']); $user=User::findOrFail($data['user_id']); $total=((float)$data['weight'])*(float)$waste->price_per_kg;
        DB::transaction(function() use($data,$waste,$user,$total,$balance,$r){
            $deposit=Deposit::create(['transaction_code'=>'DEP-'.now()->format('YmdHis').'-'.str_pad((string)($depositId=rand(100,999)),3,'0',STR_PAD_LEFT),'user_id'=>$user->id,'waste_type_id'=>$waste->id,'transaction_date'=>$data['transaction_date'],'weight'=>$data['weight'],'price_per_kg'=>$waste->price_per_kg,'total_amount'=>$total,'notes'=>$data['notes']??null,'processed_by'=>$r->user()->id,'status'=>'berhasil']);
            $balance->add($user,'deposit',$total,$deposit->id,Deposit::class,'Setoran sampah '.$waste->name,$r->user()->id);
        });
        return back()->with('success','Setoran berhasil disimpan dan saldo bertambah.');
    }
    public function cancelDeposit(Deposit $deposit,BalanceService $balance,Request $r){
        if($deposit->status==='dibatalkan') return back()->withErrors(['error'=>'Setoran sudah dibatalkan.']);
        $data=$r->validate(['cancellation_reason'=>'required|string|max:1000']);
        DB::transaction(function() use($deposit,$data,$balance,$r){ $deposit->update(['status'=>'dibatalkan','cancellation_reason'=>$data['cancellation_reason']]); $user=User::lockForUpdate()->findOrFail($deposit->user_id); $balance->adjust($user,-(float)$deposit->total_amount,$deposit->id,Deposit::class,'Pembatalan setoran '.$deposit->transaction_code,$r->user()->id); });
        return back()->with('success','Setoran dibatalkan dan saldo dikembalikan.');
    }
    public function withdrawals(Request $r){
        $requests=WithdrawalRequest::with('user')->when($r->status,fn($q,$s)=>$q->where('status',$s))->when($r->date,fn($q,$d)=>$q->whereDate('request_date',$d))->latest()->paginate(10)->withQueryString();
        return view('admin.withdrawals',compact('requests'));
    }
    public function approveWithdrawal(WithdrawalRequest $request,BalanceService $balance){
        DB::transaction(function() use($request,$balance){
            $locked=WithdrawalRequest::whereKey($request->id)->lockForUpdate()->first();
            if($locked->status!== 'menunggu') throw new \RuntimeException('Pengajuan sudah diproses.');
            $user=User::lockForUpdate()->findOrFail($locked->user_id);
            if((float)$user->balance<(float)$locked->amount) throw new \RuntimeException('Saldo nasabah tidak mencukupi.');
            $withdrawal=Withdrawal::create(['transaction_code'=>'WDR-'.now()->format('YmdHis').'-'.rand(100,999),'withdrawal_request_id'=>$locked->id,'user_id'=>$user->id,'amount'=>$locked->amount,'transaction_date'=>now(),'processed_by'=>auth()->id(),'notes'=>$locked->notes]);
            $balance->subtract($user,(float)$locked->amount,$withdrawal->id,Withdrawal::class,'Penarikan saldo disetujui',auth()->id());
            $locked->update(['status'=>'disetujui','processed_by'=>auth()->id(),'processed_at'=>now()]);
        });
        return back()->with('success','Penarikan disetujui.');
    }
    public function rejectWithdrawal(Request $r,WithdrawalRequest $request){
        $data=$r->validate(['rejection_reason'=>'required|max:1000']);
        if($request->status!== 'menunggu') return back()->withErrors(['error'=>'Pengajuan sudah diproses.']);
        $request->update(['status'=>'ditolak','rejection_reason'=>$data['rejection_reason'],'processed_by'=>$r->user()->id,'processed_at'=>now()]);
        return back()->with('success','Penarikan ditolak.');
    }
    public function reports(Request $r){
        $deposits=Deposit::with(['user','wasteType'])->where('status','berhasil')->when($r->start,fn($q,$d)=>$q->whereDate('transaction_date','>=',$d))->when($r->end,fn($q,$d)=>$q->whereDate('transaction_date','<=',$d))->latest()->get();
        $withdrawals=Withdrawal::with('user')->when($r->start,fn($q,$d)=>$q->whereDate('transaction_date','>=',$d))->when($r->end,fn($q,$d)=>$q->whereDate('transaction_date','<=',$d))->latest()->get();
        $users=User::where('role','nasabah')->get(); $wastes=WasteType::all();
        $summary=['deposit'=>(float)$deposits->sum('total_amount'),'weight'=>(float)$deposits->sum('weight'),'withdrawal'=>(float)$withdrawals->sum('amount'),'count'=>$deposits->count()+$withdrawals->count()];
        return view('admin.reports',compact('deposits','withdrawals','users','wastes','summary'));
    }
}
