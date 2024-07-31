<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['username', 'password'];
}
?>
