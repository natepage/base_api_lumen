<?php

namespace Test\Models;

use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    public $key = 'users_key';
    public $primary_key = 'custom_primary_key';
    public $limit = 40;

    protected $fillable = ['custom_primary_key'];
}
