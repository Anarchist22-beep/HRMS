<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppCountryDetail extends Model
{
    //

    protected $table = 'apps_countries_details';

    public function leads(){
        return $this->hasMany(Lead::class,'country_id');
    }
    
}
