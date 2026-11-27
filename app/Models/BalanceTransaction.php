<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceTransaction extends Model
{
    protected $fillable = ['user_id','type','reference_id','reference_type','amount','balance_before','balance_after','description','created_by'];
    protected function casts(): array { return ['amount'=>'decimal:2','balance_before'=>'decimal:2','balance_after'=>'decimal:2']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }
}
