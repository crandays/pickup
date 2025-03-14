<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderTransaction extends Model
{
    //
	    protected $fillable = [
        'provider_id',
        'amount',
    ];
	
	public function provider()
    {
        return $this->belongsTo('App\Provider');
    }
}
