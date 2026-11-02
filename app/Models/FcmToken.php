<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FcmToken extends Model
{
    protected $fillable = ["user_id","admin_id","token","platform","user_agent","last_used_at"];
    protected function casts(): array { return ["last_used_at"=>"datetime"]; }
}
