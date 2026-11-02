<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatbotLog extends Model
{
    protected $fillable = ["session_id","user_id","message","detected_intent","confidence","understood","meta"];
    protected function casts(): array { return ["understood"=>"boolean","meta"=>"array","confidence"=>"decimal:2"]; }
}
