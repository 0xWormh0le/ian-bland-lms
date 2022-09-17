<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SCORMSCOs extends Model {

	protected $table = 'scorm_scoes';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'scoid';

    public function tracks()
    {
        return $this->hasMany(SCORMTrack::class, 'scoid');
    }


}
