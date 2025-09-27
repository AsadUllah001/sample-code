<?php

namespace App\Models;

use App\Scopes\OrderByOrder;
use Illuminate\Support\Str;
use App\Traits\UUIDsAndActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldMapping extends Model
{
    use HasFactory, UUIDsAndActivity;

    protected $guarded = [];

    protected $casts = [
        'is_lookup' => 'boolean',
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
        'picklist' => 'array',
        'reference_to' => 'array',
        'custom' => 'boolean',
        'splittable' => 'boolean',
        'converted_to_usd' => 'boolean',
        'is_reference_field' => 'boolean',
        'reportable' => 'boolean',
        'reportables' => 'array',
        'should_show' => 'boolean',
        'start_date' => 'boolean',
        'end_date' => 'boolean',
        'entry_name' => 'boolean',
        'sf_required' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });

        static::addGlobalScope(new OrderByOrder);
    }

    protected function getCloneableRelations(): array
    {
        return [
            'lookupField'
        ];
    }

    public function lineItemValues()
    {
        return $this->hasMany(LineItemValue::class, 'field_mapping_id', 'id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    public function scopeInvisible($query)
    {
        return $query->where('is_visible', 0);
    }

    public function scopeReportable($query)
    {
        return $query->where('reportable', 1);
    }
}
