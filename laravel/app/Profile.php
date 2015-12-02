<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    public $timestamps = false;

    protected $guarded = ['managedBy', 'createdBy', 'createdAt', 'modifiedBy'
        , 'modifiedAt', 'modifiedCounter'];

    /**
     * Get the user that owns the phone.
     */
    public function account()
    {
        return $this->belongsTo('App\Account');
    }
}
