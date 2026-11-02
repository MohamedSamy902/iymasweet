<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Coupon extends Model
{
    protected $fillable = ["code","type","value","min_order","max_discount","starts_at","ends_at","usage_limit","per_user_limit","first_order_only","applies_to","is_active"];
    protected function casts(): array {
        return ["value"=>"decimal:2","min_order"=>"decimal:2","max_discount"=>"decimal:2","starts_at"=>"datetime","ends_at"=>"datetime","first_order_only"=>"boolean","applies_to"=>"array","is_active"=>"boolean"];
    }
    public function usages(): HasMany { return $this->hasMany(CouponUsage::class); }
}
