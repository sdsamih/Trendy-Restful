<?php

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';
    protected $fillable = ['tweet_id', 'username'];
    public $timestamps = false;
}
