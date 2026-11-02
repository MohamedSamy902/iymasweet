<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Page extends Model
{
    protected $fillable = ["slug","title_ar","title_en","content_ar","content_en","is_active","seo_title_ar","seo_title_en","seo_description_ar","seo_description_en"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
}
