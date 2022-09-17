<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElearningUser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'elearning_users';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function elearning()
    {
        return $this->belongsTo('App\Elearning', 'elearning_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    public static function getPresenterId($course_id, $company_id, $elearning_id, $user_id, $module_id)
    {
        return self::where('course_id', $course_id)
                    ->where('company_id', $company_id)
                    ->where('elearning_id', $elearning_id)
                    ->where('user_id', $user_id)
                    ->where('module_id', $module_id)
                    ->where('is_presenter', true)
                    ->first();
    }
}
