<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::observe(\App\Observers\AttachTimeStamp::class);
    }
}
