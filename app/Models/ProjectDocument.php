<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    //

    protected $fillable = ['document','project_id'];

    public function projects(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
