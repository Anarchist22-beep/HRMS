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

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id');
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class, 'project_id');
    }

}
