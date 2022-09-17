<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mutex extends Model
{
    public static $regular_ad_sync = 'REGULAR_AZURE_SYNC';
}
