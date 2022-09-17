<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Team extends Model
{
    use SoftDeletes;
    use Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teams';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'team_name'
            ]
        ];
    }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_user_id', 'id');
    }

    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public static function getByCompany($company_id)
    {
        return  self::where('company_id', $company_id)->get();
    }

    public static function getLists($company_id = null)
    {
        $data = self::select('id', 'team_name');

        if(\Auth::user()->company_id)
            $data->whereCompanyId(\Auth::user()->company_id);
        if($company_id)
            $data->whereCompanyId($company_id);
        $data = $data->orderBy('team_name')->pluck('team_name', 'id');

        return $data;
        
    }

}
