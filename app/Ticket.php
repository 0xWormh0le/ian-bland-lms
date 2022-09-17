<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function responses()
    {
        return $this->hasMany('App\TicketResponse', 'ticket_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany('App\TicketHistory', 'ticket_id', 'id');
    }

}
