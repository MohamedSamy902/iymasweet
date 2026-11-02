<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentMethod extends Model
{
    protected $fillable = ["type","name_ar","name_en","account_number","account_holder","qr_image","icon","instructions_ar","instructions_en","min_order","max_order","deposit_percent","is_active","sort_order"];
    protected function casts(): array { return ["min_order"=>"decimal:2","max_order"=>"decimal:2","deposit_percent"=>"decimal:2","is_active"=>"boolean"]; }
    public function name(?string $locale = null): string { $locale ??= app()->getLocale(); return $locale === "en" ? ($this->name_en ?: $this->name_ar) : $this->name_ar; }
}
