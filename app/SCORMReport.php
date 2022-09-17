<?php
namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SCORMReport extends Model
{

    protected $table = 'scorm_report';

    public function scorm()
    {
        return $this->belongsTo(SCORM::class, 'course');
    }

    public function learner()
    {
        return $this->belongsTo(User::class, 'user');
    }


    /**
     * Set the satisfied_status.
     *
     * @param  string  $value
     * @return string
     */
    public function setSatisfiedStatusAttribute($value)
    {
        $this->attributes['satisfied_status'] = strtolower($value);
    }

    /**
     * Set the complete_status.
     *
     * @param  string  $value
     * @return string
     */
    public function setCompleteStatusAttribute($value)
    {
        $this->attributes['complete_status'] = strtolower($value);
    }

}
