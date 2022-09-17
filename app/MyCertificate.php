<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyCertificate extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'my_certificates';

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
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function design()
    {
        return $this->belongsTo('App\CertificateDesign', 'design_id', 'id');
    }

    public static function getCertificates($user_id)
    {
        $users= array();
        if(!is_array($user_id))
        {
          $users[] = $user_id;
        }
        else
        {
          $users = $user_id;
        }
        return self::join('certificates', 'certificates.course_id', 'my_certificates.course_id')
                    ->where('certificates.active', 1)
                    ->where('company_id', \Auth::user()->company_id)
                    ->whereIn('my_certificates.user_id', $users)
                    ->where('my_certificates.is_valid', true)
                    ->orderBy('my_certificates.certified_date')
                    ->get();
    }

}
