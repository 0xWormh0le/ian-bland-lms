<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SCORMTrack extends Model {

	protected $table = 'scorm_scoes_track';

    public function scorm()
    {
        return $this->belongsTo(SCORM::class, 'scormid');
    }

    public function sco()
    {
        return $this->belongsTo(SCORMSCOs::class, 'scoid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
