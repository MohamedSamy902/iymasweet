<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HomeSection extends Model
{
    protected $fillable = ["type","title_ar","title_en","subtitle_ar","subtitle_en","config","sort_order","is_active"];
    protected function casts(): array { return ["config"=>"array","is_active"=>"boolean"]; }
}
