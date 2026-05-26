<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    //
    protected $fillable = ['transaction_id','amount','project_id','amount_paid','amount_received'];

    public function projects(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
