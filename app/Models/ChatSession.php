<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatSession extends Model
{
    protected $fillable = ["session_id","user_id","context","last_activity"];
    protected function casts(): array { return ["context"=>"array","last_activity"=>"datetime"]; }
}
