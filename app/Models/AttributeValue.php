<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AttributeValue extends Model
{
    protected $fillable = ["attribute_id","value_ar","value_en","color_hex","icon","sort_order"];
    public function attribute(): BelongsTo { return $this->belongsTo(Attribute::class); }
}
