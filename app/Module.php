<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Module extends Model
{
    use SoftDeletes;
    use Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modules';

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
                'source' => 'title',
                'method' => function ($string, $separator) {
                    $needle = [' ', '(', ')', '[', ']', '{', '}', '!', '<', '>', '?', '/', ':', ';', '&', '%', ',', '\'', '"', '@', '*'];
                    return str_replace($needle, $separator, $string);
                }
            ]
        ];
    }

    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function elearning()
    {
        return $this->hasOne('App\Elearning', 'module_id', 'id');
    }

    public function document($company_id = null)
     {
         return $this->hasOne('App\Document', 'module_id', 'id');
     }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public static function getByCourse($course_id)
    {
        return self::where('course_id', $course_id)
                    ->orderBy('order_no')
                    ->get();
    }

    public static function getNewOrderNo($course_id)
    {
        $current = self::where('course_id', $course_id)->orderBy('order_no', 'desc')->first();
        return @$current->order_no+1;
    }

    public static function getListsByCourse($course_id)
    {
        $data = self::where('course_id', $course_id)->orderBy('order_no')->get();

        $lists = [];
        foreach($data as $r)
            $lists[$r->id] = $r->type .' - '.$r->title;

        return $lists;
    }
    
}
