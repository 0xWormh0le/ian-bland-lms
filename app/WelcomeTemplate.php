<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WelcomeTemplate extends Model
{
    protected $fillable = ['company_id', 'language', 'content'];
}
