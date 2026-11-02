<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Governorate extends Model
{
    protected $fillable = ["name_ar","name_en","sort_order","is_active"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
    public function areas(): HasMany { return $this->hasMany(Area::class)->orderBy("name_ar"); }
}
