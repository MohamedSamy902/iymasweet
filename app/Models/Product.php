<?php
namespace App\Models;
use App\Enums\AvailabilityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ["category_id","slug","name_ar","name_en","short_description_ar","short_description_en","description_ar","description_en","base_price","compare_at_price","availability_type","stock_quantity","available_from","available_until","lead_time_hours","min_qty","max_qty_per_order","is_featured","is_new","is_best_seller","is_active","allow_customization","customization_config","ingredients_ar","ingredients_en","allergens_ar","allergens_en","serves_min","serves_max","weight_grams","storage_instructions_ar","storage_instructions_en","preparation_notes","sort_order","views_count","avg_rating","ratings_count","seo_title_ar","seo_title_en","seo_description_ar","seo_description_en","search_text"];
    protected function casts(): array {
        return [
            "base_price"=>"decimal:2","compare_at_price"=>"decimal:2","availability_type"=>AvailabilityType::class,
            "available_from"=>"datetime","available_until"=>"datetime","is_featured"=>"boolean","is_new"=>"boolean",
            "is_best_seller"=>"boolean","is_active"=>"boolean","allow_customization"=>"boolean","customization_config"=>"array","avg_rating"=>"decimal:2",
        ];
    }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class)->orderBy("sort_order"); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class)->orderBy("sort_order"); }
    public function addonGroups(): HasMany { return $this->hasMany(AddonGroup::class)->orderBy("sort_order"); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class); }
    public function attributeValues(): HasMany { return $this->hasMany(ProductAttributeValue::class); }
    public function reviews(): HasMany { return $this->hasMany(Review::class); }
    public function primaryImage(): ?ProductImage
    {
        $images = $this->images->where('type', '!=', 'video')->values();
        if ($images->isEmpty()) {
            $images = $this->images;
        }

        return $images->firstWhere('is_primary', true) ?? $images->first();
    }
    public function name(?string $locale = null): string { $locale ??= app()->getLocale(); return $locale === "en" ? ($this->name_en ?: $this->name_ar) : $this->name_ar; }
    public function isSoldOut(): bool { return $this->availability_type === AvailabilityType::SoldOut || ($this->stock_quantity !== null && $this->stock_quantity <= 0); }
    public function effectivePrice(): string { return (string) ($this->variants->firstWhere("is_default", true)?->price ?? $this->base_price); }
}
