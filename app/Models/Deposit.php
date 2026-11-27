<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = ['transaction_code','user_id','waste_type_id','transaction_date','weight','price_per_kg','total_amount','notes','processed_by','status','cancellation_reason'];
    protected function casts(): array { return ['transaction_date'=>'date','weight'=>'decimal:2','price_per_kg'=>'decimal:2','total_amount'=>'decimal:2']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function wasteType(): BelongsTo { return $this->belongsTo(WasteType::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class,'processed_by'); }
}
