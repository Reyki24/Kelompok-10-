<?php
namespace App\Services;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class BalanceService
{
    public function add(User $user, string $type, float|int|string $amount, ?int $referenceId, ?string $referenceType, string $description, ?int $createdBy = null): void
    {
        $before = (float) $user->balance;
        $after = $before + (float) $amount;
        $user->forceFill(['balance' => number_format($after, 2, '.', '')])->save();
        BalanceTransaction::create(['user_id'=>$user->id,'type'=>$type,'reference_id'=>$referenceId,'reference_type'=>$referenceType,'amount'=>number_format((float)$amount,2,'.',''),'balance_before'=>number_format($before,2,'.',''),'balance_after'=>number_format($after,2,'.',''),'description'=>$description,'created_by'=>$createdBy]);
    }
    public function subtract(User $user, float|int|string $amount, ?int $referenceId, ?string $referenceType, string $description, ?int $createdBy = null): void
    {
        $before = (float) $user->balance;
        $after = $before - (float) $amount;
        if ($after < 0) throw new \RuntimeException('Saldo tidak mencukupi.');
        $user->forceFill(['balance' => number_format($after, 2, '.', '')])->save();
        BalanceTransaction::create(['user_id'=>$user->id,'type'=>'withdrawal','reference_id'=>$referenceId,'reference_type'=>$referenceType,'amount'=>number_format((float)$amount,2,'.',''),'balance_before'=>number_format($before,2,'.',''),'balance_after'=>number_format($after,2,'.',''),'description'=>$description,'created_by'=>$createdBy]);
    }
    public function adjust(User $user, float|int|string $amount, ?int $referenceId, ?string $referenceType, string $description, ?int $createdBy = null): void
    {
        $before=(float)$user->balance; $after=$before+(float)$amount;
        if($after<0) throw new \RuntimeException('Saldo tidak mencukupi.');
        $user->forceFill(['balance'=>number_format($after,2,'.','')])->save();
        BalanceTransaction::create(['user_id'=>$user->id,'type'=>'adjustment','reference_id'=>$referenceId,'reference_type'=>$referenceType,'amount'=>number_format((float)$amount,2,'.',''),'balance_before'=>number_format($before,2,'.',''),'balance_after'=>number_format($after,2,'.',''),'description'=>$description,'created_by'=>$createdBy]);
    }
}
