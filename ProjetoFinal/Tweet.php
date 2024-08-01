<?php
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{

    protected $table = 'tweets';
    protected $fillable = ['username', 'content'];
    public $timestamps = true;
    
    public function likes()
    {
        return $this->hasMany('App\Like');
    }
}
