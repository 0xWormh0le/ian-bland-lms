<?php

namespace App;

use App\CourseConfig;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseUser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_users';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['course_id'] ;

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function enrolledby()
    {
        return $this->belongsTo('App\User', 'enrolled_by', 'id');
    }

    public function results()
    {
        return $this->hasMany('App\CourseResult', 'courseuser_id');
    }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function getEnrolledUsers($company_id = null)
    {
        $data = self::select('users.first_name', 'users.last_name', 'users.id')
                    ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                    ->whereNull('users.deleted_at')
                    ->where('users.active', true)
                    ->where('users.is_suspended', false);
        if ($company_id) {
            $data->where('users.company_id', $company_id);
        }

        $data = $data->orderBy('first_name')->get();
        return $data;
    }

    public static function getByUser($user_id)
    {
        return self::select('course_users.*')
                     ->join('courses','courses.id', 'course_users.course_id')
                     ->where('user_id', $user_id)
                     ->whereNull('courses.deleted_at')
                     ->get();
    }

    public static function getCompletedByUser($user_id)
    {
        return self::where('user_id', $user_id)
                    ->where('completed', true)
                    ->get();
    }

    public static function myCourse($course_id, $user_id)
    {
        return self::select('course_users.*')
                    ->join('courses','courses.id', 'course_users.course_id')
                    ->join('users','users.id', 'course_users.user_id')
                    ->where('course_id', $course_id)
                    ->where('user_id', $user_id)
                    ->whereNull('users.deleted_at')
                    ->whereNull('courses.deleted_at')
                    ->first();
    }

    public static function myCourseWithTrashed($course_id, $user_id)
    {
        return self::withTrashed()->select('course_users.*')
                    ->join('courses','courses.id', 'course_users.course_id')
                    ->join('users','users.id', 'course_users.user_id')
                    ->where('course_id', $course_id)
                    ->where('user_id', $user_id)
                    ->first();
    }

    public static function updateResult($course_id, $user_id)
    {

        $courseUser = self::where('course_id', $course_id)
                            ->where('user_id', $user_id)
                            ->first();

        // $rules = null ;

        // if ($courseUser && $courseUser->course) {
        //    $rules = $courseUser->course->config;
        // }

        // if ($rules != null) {
            $completion_modules = [];

            foreach ($courseUser->course->modules as $m) {
                $completion_modules[] = $m->id;
            }

            $query = \App\CourseConfig::where('course_id', $course_id);

            if (!auth()->user()->isSysAdmin()) {
                $query = $query->where('company_id', auth()->user()->company_id);
            } else {
                $query = $query->whereNull('company_id');
            }

            $query->forceDelete();

            $config = new CourseConfig;

            if (!auth()->user()->isSysAdmin()) {
                $config->company_id = auth()->user()->company_id;
            }

            $config->course_id = $course_id;
            $config->transversal_rule = 'none';
            $config->completion_rule = 'any';
            $config->completion_modules = implode(',', $completion_modules);
            $config->completion_percentage = 100;
            $config->learning_path = '';
            $config->get_certificate = false;
            $config->created_by = $courseUser->course->created_by;
            $config->save();
            $rules = CourseConfig::find($config->id);
        // }

        $moduleIds = [];
        $moduleCount = 0;
        $modulePassed = [];

        if (@$courseUser->course->modules) {
            foreach ($courseUser->course->modules as $module) {
                $courseResult = \App\CourseResult::where('courseuser_id', $courseUser->id)
                                                ->where('module_id', $module->id)
                                                ->first();
                if ($courseResult && $courseResult->satisfied_status == 'Passed') {
                    $modulePassed[] = $courseResult->module_id;
                }

                $moduleIds[] = $module->id;
                $moduleCount++;
            }
        }

        $completed = false;
        $completion_percentage = 100;

        switch ($rules->completion_rule) {
            case 'certain':
                $completion_modules = explode(',', $rules->completion_modules);
                
                if (count(array_diff($completion_modules, $modulePassed)) == 0) {
                    $completed = true;
                }

                if ($moduleCount > 0) {
                    $completion_percentage = round((count($modulePassed) / $moduleCount) * 100, 2);
                } else {
                    $completion_percentage = 0.0 ;
                }
        
                if ($completed == true &&
                    $completion_percentage < (float)@$rules->completion_percentage) {
                    $completed = false;
                }

                break;

            case 'any':
                if (count($modulePassed) > 0) {
                    $completed = true;
                }
                break;

            case 'all':
            default:
                if (count($modulePassed) == $moduleCount) {
                    $completed = true;
                }
                break;
        }

        $courseUser->completion_percentage = $completion_percentage;
        $courseUser->completed = $completed;
        $courseUser->completion_date = $completed ? date('Y-m-d H:i:s') : null;

        $certificate = \App\MyCertificate::withTrashed()->where('course_id', $course_id)
                            ->where('user_id', $user_id)
                            ->first();

        if ($completed) {
            if (!$certificate) {
                $certificate = new \App\MyCertificate;
            }

            $certificate->course_id = $course_id;
            $certificate->user_id = $user_id;
            $certificate->design_id = 0;
            $certificate->certified_date = date('Y-m-d');
            $certificate->is_valid = true;
            $certificate->deleted_at = null;
            $certificate->save();
        } else {
            if ($certificate) {
                $certificate->delete();
            }
        }

        return $courseUser->save();
    }

    public function getStartDateAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    
    public function setStartDateAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        $this->attributes['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }


}
