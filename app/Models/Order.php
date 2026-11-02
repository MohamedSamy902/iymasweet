<?php
namespace App\Models;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    use SoftDeletes;
    protected $fillable = ["number","user_id","guest_name","guest_phone","status","payment_method_id","payment_status","subtotal","discount_total","delivery_fee","total","coupon_id","coupon_code","delivery_date","delivery_slot_label","delivery_slot_start","delivery_slot_end","address_snapshot","customer_notes","admin_notes","cancelled_reason","cancelled_by","placed_at","confirmed_at","delivered_at","payment_due_at","ip","user_agent"];
    protected function casts(): array {
        return [
            "status"=>OrderStatus::class,"payment_status"=>PaymentStatus::class,
            "subtotal"=>"decimal:2","discount_total"=>"decimal:2","delivery_fee"=>"decimal:2","total"=>"decimal:2",
            "address_snapshot"=>"array","delivery_date"=>"date","placed_at"=>"datetime","confirmed_at"=>"datetime",
            "delivered_at"=>"datetime","payment_due_at"=>"datetime",
        ];
    }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function paymentMethod(): BelongsTo { return $this->belongsTo(PaymentMethod::class); }
    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function statusHistory(): HasMany { return $this->hasMany(OrderStatusHistory::class)->orderBy("created_at"); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function latestPayment(): HasOne { return $this->hasOne(Payment::class)->latestOfMany(); }
    public function customerName(): string { return $this->user?->name ?? $this->guest_name ?? "-"; }
    public function customerPhone(): string { return $this->user?->phone ?? $this->guest_phone ?? "-"; }
}
