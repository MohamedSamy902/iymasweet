<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationTemplate extends Model
{
    protected $fillable = ["key","title_ar","title_en","body_ar","body_en","variables","channels","is_active","icon","deep_link"];
    protected function casts(): array { return ["variables"=>"array","channels"=>"array","is_active"=>"boolean"]; }
}
