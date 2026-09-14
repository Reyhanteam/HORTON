<?php

namespace App\Models;

class PasswordResetToken extends HortonModel
{
    protected $table = 'password_reset_tokens';

    public $timestamps = false;
}
