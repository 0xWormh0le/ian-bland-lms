<?php
namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SCORM extends Model {

	protected $table = 'scorm';

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function elearning()
    {
        return $this->hasOne(Elearning::class, 'scorm_id');
    }

    public function tracks()
    {
        return $this->hasMany(SCORMTrack::class, 'scormid');
    }


}
