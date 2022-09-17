<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseResult extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_results';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function module()
    {
        return $this->belongsTo('App\Module', 'module_id', 'id');
    }
    public function courseuser()
    {
        return $this->belongsTo('App\CourseUser', 'courseuser_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany('App\CourseResultHistory', 'courseresult_id', 'id');
    }


    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function getModuleResult($courseuser_id, $module_id)
    {
        return self::where('courseuser_id', $courseuser_id)
                    ->where('module_id', $module_id)
                    ->first();
    }

    public static function getModuleResultWithTrashed($courseuser_id, $module_id)
    {
        return self::withTrashed()->where('courseuser_id', $courseuser_id)
                    ->where('module_id', $module_id)
                    ->first();
    }

    public static function myCourse($course_id, $user_id)
    {
        return self::where('course_id', $course_id)
                    ->where('user_id', $user_id)
                    ->first();
    }


}
