<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ["parent_id","slug","name_ar","name_en","description_ar","description_en","image","icon","color","sort_order","is_active","is_featured","seo_title_ar","seo_title_en","seo_description_ar","seo_description_en"];
    protected function casts(): array { return ["is_active"=>"boolean","is_featured"=>"boolean"]; }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, "parent_id"); }
    public function children(): HasMany { return $this->hasMany(self::class, "parent_id")->orderBy("sort_order"); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function name(?string $locale = null): string { $locale ??= app()->getLocale(); return $locale === "en" ? ($this->name_en ?: $this->name_ar) : $this->name_ar; }
}
