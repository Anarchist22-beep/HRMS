<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    //
    protected $fillable = ['transaction_id','amount','project_id'];
}
