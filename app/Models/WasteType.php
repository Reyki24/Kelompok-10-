<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WasteType extends Model
{
    /** @use HasFactory<\Database\Factories\WasteTypeFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = ['name','price_per_kg','description','icon','status'];
    protected function casts(): array { return ['price_per_kg'=>'decimal:2']; }
    public function deposits(): HasMany { return $this->hasMany(Deposit::class); }
}
