<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elearning extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'elearnings';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function module()
    {
        return $this->belongsTo('App\Module', 'module_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'elearning_users', 'elearning_id', 'user_id')->withPivot('complete_status')->withTimestamps();
      //  return $this->belongsToMany(User::class, 'elearning_user', 'elearning_id', 'user_id')->withPivot('completed')->withTimestamps();
    }

    public function scorm()
    {
        return $this->belongsTo(SCORM::class, 'scorm_id');
    }

	public function getScormScoesTrackRecordByUserId($userId)
	{
		if (!empty($this->scormScoesTrackRecord[$userId])) {
			return $this->scormScoesTrackRecord[$userId];
		}


                		$results = \DB::table('scorm_report')->select(\DB::raw($sql))->where('user', $userId)->where('course', $this->scorm_id)->get();

                		foreach($results as $result) {
                            $this->scormScoesTrackRecord[$userId]['complete_status'] = $result->complete_status;
                            $this->scormScoesTrackRecord[$userId]['satisfied_status'] = $result->satisfied_status;
                            $this->scormScoesTrackRecord[$userId]['score'] = $result->score;
                            $this->scormScoesTrackRecord[$userId]['total_time'] = $result->total_time;
                            $this->scormScoesTrackRecord[$userId]['attempt'] = $result->attempt;
                		}

                		return $this->scormScoesTrackRecord[$userId];
	}

}
