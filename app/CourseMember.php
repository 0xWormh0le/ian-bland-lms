<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMember extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_members';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = ['course_id'];
    
    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }


    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function getByCourseCompany($course_id, $company_id)
    {
        return self::where('course_id', $course_id)
                    ->where('company_id', $company_id)
                    ->get();
    }
}
