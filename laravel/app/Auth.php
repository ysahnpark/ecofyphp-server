<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    //
    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['security_password', 'security_activationCode', 'security_securityAnswer'];

    protected $guarded = ['managedBy', 'createdBy', 'createdAt', 'modifiedBy'
        , 'modifiedAt', 'modifiedCounter'
        , 'sessionTimestamp', 'lastLogin'];

    /**
     * Get the user that owns the phone.
     */
    public function account()
    {
        return $this->belongsTo('App\Account',  'accountUuid',  'uuid');
    }
}
