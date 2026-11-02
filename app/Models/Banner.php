<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model
{
    protected $fillable = ["image_mobile","image_desktop","title_ar","title_en","subtitle_ar","subtitle_en","cta_text_ar","cta_text_en","link","position","starts_at","ends_at","sort_order","is_active"];
    protected function casts(): array { return ["starts_at"=>"datetime","ends_at"=>"datetime","is_active"=>"boolean"]; }
}
