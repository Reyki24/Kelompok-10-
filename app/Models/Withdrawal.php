<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = ['transaction_code','withdrawal_request_id','user_id','amount','transaction_date','processed_by','notes'];
    protected function casts(): array { return ['amount'=>'decimal:2','transaction_date'=>'datetime']; }
    public function request(): BelongsTo { return $this->belongsTo(WithdrawalRequest::class,'withdrawal_request_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class,'processed_by'); }
}
