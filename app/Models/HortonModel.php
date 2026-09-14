<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class HortonModel extends Model
{
    /**
     * The Horton schema is already managed outside Laravel migrations.
     * Models intentionally allow mass assignment; validation and authorization
     * belong to the Application/Dashboard layers.
     */
    protected $guarded = [];
}
