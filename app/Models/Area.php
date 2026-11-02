<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Area extends Model
{
    protected $fillable = ["governorate_id","name_ar","name_en","delivery_fee","min_order","eta_minutes","free_delivery_over","is_active"];
    protected function casts(): array { return ["delivery_fee"=>"decimal:2","min_order"=>"decimal:2","free_delivery_over"=>"decimal:2","is_active"=>"boolean"]; }
    public function governorate(): BelongsTo { return $this->belongsTo(Governorate::class); }
}
