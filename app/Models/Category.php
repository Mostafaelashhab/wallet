<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'icon', 'color', 'keywords'];

    protected $casts = [
        'keywords' => 'array',
    ];

    public function name(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public static function detect(string $description): ?self
    {
        $desc = mb_strtolower($description);
        foreach (static::all() as $cat) {
            foreach (($cat->keywords ?? []) as $kw) {
                if (mb_stripos($desc, mb_strtolower($kw)) !== false) {
                    return $cat;
                }
            }
        }
        return null;
    }
}
