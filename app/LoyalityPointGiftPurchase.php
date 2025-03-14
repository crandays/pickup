<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Provider;
use App\User;

class LoyalityPointGiftPurchase extends Model
{
    protected $table = 'loyality_point_gift_purchases';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'buyer',
        'buyer_id',
        'loyality_point_gift_id',
        'points_on_purchase',
    ];
    protected $hidden = [];
    protected $guarded = ['id'];


    public function gift()
    {
        return $this->hasOne('App\LoyalityPointGift', 'id', 'loyality_point_gift_id');
    }

    public function user($id)
    {
        return User::where('id',$id)->first();
    }

    public function provider($id)
    {
        return Provider::where('id',$id)->first();
    }


}
