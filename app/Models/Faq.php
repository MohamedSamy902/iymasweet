<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Faq extends Model
{
    protected $fillable = ["category","question_ar","question_en","answer_ar","answer_en","sort_order","is_active"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
}
