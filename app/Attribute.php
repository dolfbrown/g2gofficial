<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;

    const TYPE_COLOR   = 1;         //Color/Pattern
    const TYPE_RADIO   = 2;
    const TYPE_SELECT  = 3;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                    'shop_id',
                    'name',
                    'attribute_type_id',
                    'order',
                ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the AttributeType for the Attribute.
     */
    public function attributeType()
    {
        return $this->belongsTo(AttributeType::class);
    }

    /**
     * Attribute has many AttributeValue
     */
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Get the products for the Attribute.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product')
                    ->withPivot('attribute_value_id')
                    ->withTimestamps();
    }


    /**
     * Return extra classes to views based on type
     *
     * @return str
     */
    public function getCssClassesAttribute()
    {
        switch ($this->attribute_type_id) {
            case static::TYPE_COLOR: return 'color-options';
            case static::TYPE_RADIO: return 'radioSelect';
            case static::TYPE_SELECT: return 'selectBoxIt';
            default: return 'selectBoxIt';
        }
    }
}
