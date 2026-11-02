<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Attribute extends Model
{
    protected $fillable = ["key","name_ar","name_en","type","is_filterable","show_on_product_page","sort_order","applies_to_categories"];
    protected function casts(): array { return ["is_filterable"=>"boolean","show_on_product_page"=>"boolean","applies_to_categories"=>"array"]; }
    public function values(): HasMany { return $this->hasMany(AttributeValue::class)->orderBy("sort_order"); }
}
