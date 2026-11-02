<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class CartItem extends Model
{
    protected $fillable = ["cart_id","purchasable_type","purchasable_id","variant_id","quantity","addons","customization","reference_image_path","unit_price_snapshot"];
    protected function casts(): array { return ["addons"=>"array","customization"=>"array","unit_price_snapshot"=>"decimal:2"]; }
    public function cart(): BelongsTo { return $this->belongsTo(Cart::class); }
    public function purchasable(): MorphTo { return $this->morphTo(); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, "variant_id"); }
    public function lineTotal(): float { return (float) $this->unit_price_snapshot * $this->quantity; }
}
