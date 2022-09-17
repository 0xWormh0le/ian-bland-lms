<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Azure extends Model
{
    //
    protected $fillable = ['tenant_id', 'client_id', 'client_secret'];
}
