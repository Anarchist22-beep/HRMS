<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //

    protected $fillable = ['name','description','document','status'];

    public function users(){
        return $this->hasMany(User::class,'project_id');
    }

}
