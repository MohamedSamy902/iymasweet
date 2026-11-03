<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClosedDate extends Model
{
    protected $fillable = ["date","reason_ar","reason_en","is_full_day"];
    protected function casts(): array { return ["date"=>"date","is_full_day"=>"boolean"]; }
}
