<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AddonOption extends Model
{
    protected $fillable = ["addon_group_id","name_ar","name_en","price","is_active","sort_order"];
    protected function casts(): array { return ["price"=>"decimal:2","is_active"=>"boolean"]; }
    public function group(): BelongsTo { return $this->belongsTo(AddonGroup::class, "addon_group_id"); }
}
