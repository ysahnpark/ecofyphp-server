<?php
namespace App\Modules\Account;

use Illuminate\Database\Eloquent\Model;

class Account extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    // model configuration
    protected $primaryKey = 'sid';
    public $timestamps = false;
    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['createdAt', 'modifiedAt', 'disabledAt', 'lastLogin'];

    protected $guarded = ['sid', 'managedBy', 'createdBy', 'createdAt', 'modifiedBy'
        , 'modifiedAt', 'modifiedCounter'
    , 'kind' /*, 'lastLogin'*/];

    /**
     * Get the phone record associated with the user.
     */
    public function profile()
    {
        return $this->hasOne('App\Modules\Account\Profile', 'accountUuid', 'uuid');
    }


    /**
     * Get the phone record associated with the user.
     */
    public function auths()
    {
        return $this->hasMany('App\Modules\Auth\Auth', 'accountUuid', 'uuid');
    }

    // Registering delete event for cleaning up relations
    protected static function boot() {
        parent::boot();

        static::deleting(function($account) { // before delete() method call this
             $account->profile()->delete();
             $account->auths()->delete();
        });
    }

    // From Authenticatable
    /**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
    {
        $this->uuid;
    }
	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
    {
        return null;
    }
	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
    {

    }
	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
    {

    }
	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
    {

    }
}
