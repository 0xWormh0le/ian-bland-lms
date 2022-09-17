<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SCORMSeqRollupRule extends Model {

	protected $table = 'scorm_seq_rolluprule';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rollupruleid';
}
