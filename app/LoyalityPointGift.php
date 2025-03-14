<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyalityPointGift extends Model
{
    protected $table = 'loyality_point_gifts';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'price_in_points',
        'status',
    ];
    protected $hidden = [];
    protected $guarded = ['id'];


    public function gift_purchases()
    {
        return $this->hasMany('App\LoyalityPointGiftPurchase', 'loyality_point_gift_id', 'id');
    }


}
