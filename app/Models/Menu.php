<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Menu extends Model
{
    protected $fillable = ["location","name"];
    public function items(): HasMany { return $this->hasMany(MenuItem::class)->where("is_active", true)->orderBy("sort_order"); }
}
