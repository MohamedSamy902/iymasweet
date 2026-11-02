<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ChatbotIntent extends Model
{
    protected $fillable = ["key","name_ar","name_en","keywords","patterns","priority","is_active"];
    protected function casts(): array { return ["keywords"=>"array","patterns"=>"array","is_active"=>"boolean"]; }
    public function responses(): HasMany { return $this->hasMany(ChatbotResponse::class, "intent_id"); }
}
