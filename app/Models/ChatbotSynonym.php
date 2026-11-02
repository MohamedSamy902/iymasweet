<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatbotSynonym extends Model
{
    protected $fillable = ["canonical","synonym"];
}
