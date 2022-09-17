<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCompany extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_companies';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['company_id'];

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function getModule($type, $module_id = null)
    {
      /*  if($type == 'Classroom')
            $data = \App\Classroom::select('*');
        else
            $data = \App\Webex::select('*');

        $data->where('company_id', $this->company_id)
                ->where('course_id', $this->course_id);

        if($module_id){
            $data->where('module_id', $module_id);
            $data = $data->first();
        }
        else
            $data = $data->get();
        return $data;*/
    }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function findByCourseCompany($course_id, $company_id)
    {
        return self::where('course_id', $course_id)
                    ->where('company_id', $company_id)
                    ->first();
    }

    public static function getCourseByCompany($company_id)
    {
        return self::select('courses.id', 'courses.title')
                    ->leftJoin('courses', 'course_companies.course_id', '=', 'courses.id')
                    ->where('course_companies.company_id', $company_id)
                    ->orderBy('courses.title')
                    ->get();
    }
}
