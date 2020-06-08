<?php

namespace Tests;

use Laravel\Passport\HasApiTokens;

class User extends \Illuminate\Foundation\Auth\User
{
    use HasApiTokens;
}
