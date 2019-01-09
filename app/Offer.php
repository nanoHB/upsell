<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offer';

    protected $fillable = ['trigger_place','name','type','title','description','shop_id','start_day','end_day','active'];
}
