<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreamSession extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stream_sessions';
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
