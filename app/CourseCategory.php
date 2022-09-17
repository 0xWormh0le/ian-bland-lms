<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class CourseCategory extends Model
{
    use SoftDeletes;
    use Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_categories';

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


    public function subCategories($parentId)
    {
       $subCategories = CourseCategory::where("parent", $parentId)->get();
       return $subCategories;
    }

    public static function getCategoryTitle($catId)
    {
      $catTitle = CourseCategory::select("title")->where("id", $catId)->first();
      if($catTitle)
        return $catTitle->title;
      else
        return '';  
    }

}
