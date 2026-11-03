<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MenuItem extends Model
{
    protected $fillable = ["menu_id","label_ar","label_en","url","route","icon","sort_order","is_active"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
    public function menu(): BelongsTo { return $this->belongsTo(Menu::class); }
}
