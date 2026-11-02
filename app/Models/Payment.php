<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Payment extends Model
{
    protected $fillable = ["order_id","payment_method_id","amount","status","receipt_path","receipt_hash","sender_name","sender_phone_or_reference","rejection_reason","reviewed_by","reviewed_at","submitted_at"];
    protected function casts(): array { return ["amount"=>"decimal:2","reviewed_at"=>"datetime","submitted_at"=>"datetime"]; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function method(): BelongsTo { return $this->belongsTo(PaymentMethod::class, "payment_method_id"); }
    public function reviewer(): BelongsTo { return $this->belongsTo(Admin::class, "reviewed_by"); }
}
