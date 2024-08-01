<?php
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{

    protected $table = 'tweets';
    protected $fillable = ['username', 'content','user_id'];
    public $timestamps = true;
    
    public function likes()
    {
        return $this->hasMany('App\Like');
    }
}
