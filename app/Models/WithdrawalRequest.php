<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WithdrawalRequest extends Model
{
    protected $fillable = ['request_code','user_id','amount','request_date','status','notes','rejection_reason','processed_by','processed_at'];
    protected function casts(): array { return ['amount'=>'decimal:2','request_date'=>'datetime','processed_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class,'processed_by'); }
    public function withdrawal(): HasOne { return $this->hasOne(Withdrawal::class); }
}
