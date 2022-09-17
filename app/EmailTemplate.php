<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_templates';

    protected $fillable = ['company_id', 'template_name', 'slug', 'language'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;



    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

}
