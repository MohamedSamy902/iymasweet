<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Address extends Model
{
    protected $fillable = ["user_id","label","recipient_name","phone","governorate_id","area_id","street","building","floor","apartment","landmark","lat","lng","notes_for_courier","is_default"];
    protected function casts(): array { return ["is_default"=>"boolean","lat"=>"decimal:7","lng"=>"decimal:7"]; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function governorate(): BelongsTo { return $this->belongsTo(Governorate::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function toSnapshot(): array {
        return [
            "label"=>$this->label,"recipient_name"=>$this->recipient_name,"phone"=>$this->phone,
            "governorate"=>$this->governorate?->name_ar,"area"=>$this->area?->name_ar,
            "street"=>$this->street,"building"=>$this->building,"floor"=>$this->floor,"apartment"=>$this->apartment,
            "landmark"=>$this->landmark,"lat"=>$this->lat,"lng"=>$this->lng,"notes_for_courier"=>$this->notes_for_courier,
            "area_id"=>$this->area_id,"governorate_id"=>$this->governorate_id,"delivery_fee"=>$this->area?->delivery_fee,
        ];
    }
}
