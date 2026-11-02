<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model
{
    protected $fillable = ["log_name","description","subject_type","subject_id","causer_type","causer_id","properties","event","batch_uuid"];
    protected function casts(): array { return ["properties"=>"array"]; }
}
