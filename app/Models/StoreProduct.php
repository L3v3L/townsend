<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class StoreProduct extends Model
{
    use HasFactory;

    public $table = 'store_products';

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(
            Section::class,
            'store_products_section',
            'store_product_id',
            'section_id',
            'id',
            'id'
        )
            ->withPivot('position')
            ->orderBy('position', 'ASC');
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }

    public function getImageAttribute(): string
    {
        $imagesDomain = "https://img.tmstor.es/";

        if (strlen($this->image_format) > 2) {
            return "{$imagesDomain}/$this->id.{$this->image_format}";
        }

        return "{$imagesDomain}noimage.jpg";
    }

    public function getConvertedPriceAttribute(): float
    {
        switch (session(['currency'])) {
            case "USD":
                return $this->dollar_price;
            case "EUR":
                return $this->euro_price;
            default:
                return $this->price;
        }
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereDeleted(0)
            ->whereAvailable(1);
    }

    public function scopeExcludeCountry(
        Builder $query,
        string $countryCode
    ): Builder {
        return $query->where(function ($subquery) use ($countryCode) {
            $subquery
                ->whereNull('disabled_countries')
                ->orWhere('disabled_countries', '')
                ->orWhere(function ($subSubquery) use ($countryCode) {
                    $subSubquery
                        ->where('disabled_countries', 'not like', "%,{$countryCode},%")
                        ->where('disabled_countries', 'not like', "{$countryCode},%")
                        ->where('disabled_countries', 'not like', "%,{$countryCode}")
                        ->where('disabled_countries', 'not like', $countryCode);
                });
        });
    }

    public function scopeLaunched(Builder $query): Builder
    {
        return $query->where(function ($subquery) {
            $subquery
                ->whereNull('launch_date')
                ->orWhere('launch_date', '<=', now());
        });
    }

    public function scopeExcludeRemoved(Builder $query): Builder
    {
        return $query->where(function ($subquery) {
            $subquery
                ->whereNull('remove_date')
                ->orWhere('remove_date', '>=', now());
        });
    }

    public function scopeHasSection(
        Builder $query,
        int|string $section
    ): Builder {
        return $query->whereHas('sections', function ($query) use ($section) {
            if (is_numeric($section)) {
                $query->where('sections.id', $section);
            } else {
                $query->where('description', 'LIKE', $section);
            }
        });
    }
}
