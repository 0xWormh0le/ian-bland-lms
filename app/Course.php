<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Course extends Model
{
    use SoftDeletes;
    use Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function modules()
    {
        return $this->hasMany('App\Module', 'course_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany('App\Document', 'course_id', 'id');
    }


    public function configRelation()
    {
        return $this->hasOne('App\CourseConfig', 'course_id', 'id');
    }

    public function config()
    {
      if(\Auth::user()->isSysAdmin())
            return $this->configRelation()->whereNull('company_id');
      else {
            return $this->configRelation()->where('company_id', \Auth::user()->company_id);
        }

    }
    public function certificate()
    {
        $company_id = \Auth::user()->company_id;
        $relation = $this->hasOne('App\Certificate', 'course_id', 'id');
        
        if ($company_id) {
            $relation->where('company_id', $company_id);
        }
     
        return $relation->withTrashed();
    }


    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public function getDeadlineDateAttribute($value){
        if(empty($value)) { return; }
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    public function setDeadlineDateAttribute($value){
        if(empty($value)) { return; }
        $this->attributes['deadline_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function category()
    {
        return $this->belongsTo(\App\CourseCategory::class);
    }

    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public static function updateModuleInfo($id)
    {
      $course = self::find($id);
      $has_elearning = $has_document = false;
      foreach($course->modules as $module)
      {
          if($module->type == 'Elearning')
              $has_elearning = true;
          elseif($module->type == 'Document')
              $has_document = true;
      }
    /*  \DB::table('courses')->where('id', $course->id)->update([
          'has_elearning' => $has_elearning,
          'has_document' => $has_document,
      ]);*/
    }

    public function registrants()
    {
        return $this->belongsToMany('App\User', 'course_users', 'course_id', 'user_id')
            ->withPivot( 'active', 'completed', 'completion_date')->withTimestamps();
    }



}
