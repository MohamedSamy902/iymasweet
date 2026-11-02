<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class OrderItem extends Model
{
    protected $fillable = ["order_id","purchasable_type","purchasable_id","name_ar","name_en","image","variant_name","unit_price","quantity","addons","customization","reference_image_path","line_total","availability_type_at_order"];
    protected function casts(): array { return ["unit_price"=>"decimal:2","line_total"=>"decimal:2","addons"=>"array","customization"=>"array"]; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function purchasable(): MorphTo { return $this->morphTo(); }
}
