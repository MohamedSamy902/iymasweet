<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StockMovement extends Model
{
    protected $fillable = ["product_id","variant_id","change","reason","order_id","admin_id"];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
