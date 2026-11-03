<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ChatbotResponse extends Model
{
    protected $fillable = ["intent_id","text_ar","text_en","buttons","action","weight"];
    protected function casts(): array { return ["buttons"=>"array"]; }
    public function intent(): BelongsTo { return $this->belongsTo(ChatbotIntent::class, "intent_id"); }
}
