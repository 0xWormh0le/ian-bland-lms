<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScormConfiguration extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scorm_configurations';

    protected $fillable = ['scormEngineServiceUrl', 'appId', 'securityKey', 'originString', 'proxy'];
    protected $guarded = ['id'];
    //public $incrementing = false;

    public function getRuleSet($set){
        switch($set){
            case 'creating':
                return array(
                    'scormEngineServiceUrl' => 'required',
                    'appId' => 'required',
                    'securityKey' => 'required',
                    'originString' => 'required'
                );
            case 'updating':
                return array(
                    'scormEngineServiceUrl' => 'required',
                    'appId' => 'required',
                    'securityKey' => 'required',
                    'originString' => 'required'
                );
            default:
                break;
        }
        return [];
    }
}
