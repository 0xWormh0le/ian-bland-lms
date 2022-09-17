<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificateConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_certificate_config';

    protected $fillable = ["company_id"];


    public function getValidityDurationAttribute($value){
        if(empty($value)) { return; }
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    public function setValidityDurationAttribute($value){
        if(empty($value)) { return; }
        $this->attributes['validity_duration'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

}
