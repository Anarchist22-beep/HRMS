<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    //

    protected $fillable = ['name','email','phone_no','description','location','amount','qualified','user_id','profile_link'];


    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
