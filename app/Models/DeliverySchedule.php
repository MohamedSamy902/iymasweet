<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeliverySchedule extends Model
{
    protected $fillable = ["day_of_week","is_open","opens_at","closes_at","slot_duration_minutes","order_cutoff_time","next_day_cutoff_time","capacity"];
    protected function casts(): array { return ["is_open"=>"boolean"]; }
}
