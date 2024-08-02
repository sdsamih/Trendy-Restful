<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['username', 'password','cargo'];

    public function likes()
    {
        return $this->hasMany('Like', 'user_id');
    }
}

?>
