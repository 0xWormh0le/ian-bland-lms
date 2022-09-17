<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Company extends Model
{
    use SoftDeletes;
    use Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';
    
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
                'source' => 'company_name'
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

    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public static function getLists()
    {
        $data = self::select('id', 'company_name');
        if(\Auth::user()->company_id)
            $data->where('id', \Auth::user()->company_id);

        $data = $data->pluck('company_name', 'id');
        return $data;
    }

}
