<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyEmailConfig extends Model
{
  protected $table = 'company_email_config';

  protected $fillable = ['company_id'];
}
