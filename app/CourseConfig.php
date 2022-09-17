<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseConfig extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_configs';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function listRules()
    {
        return [
            'transversal_rule' => [
                'none' => 'None',
                'sequential' => 'Modules must all be seen and completed sequentially'
            ],
            'completion_rule' => [
                'all' => 'All modules must be completed',
                'certain' => 'Certain modules must be completed',
                'percentage' => 'A percentage of modules must be completed',
                'any' => 'Any modules must be completed'
            ],
        ];
    }
}
