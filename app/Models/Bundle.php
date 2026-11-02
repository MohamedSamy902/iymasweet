<?php
namespace App\Models;
use App\Enums\AvailabilityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bundle extends Model
{
    use SoftDeletes;
    protected $fillable = ["slug","name_ar","name_en","description_ar","description_en","image","type","price","compare_at_price","items_count","min_items","max_items","availability_type","lead_time_hours","is_active","sort_order"];
    protected function casts(): array { return ["price"=>"decimal:2","compare_at_price"=>"decimal:2","availability_type"=>AvailabilityType::class,"is_active"=>"boolean"]; }
    public function items(): HasMany { return $this->hasMany(BundleItem::class); }
    public function allowedProducts(): BelongsToMany { return $this->belongsToMany(Product::class, "bundle_allowed_products"); }
    public function name(?string $locale = null): string { $locale ??= app()->getLocale(); return $locale === "en" ? ($this->name_en ?: $this->name_ar) : $this->name_ar; }
}
