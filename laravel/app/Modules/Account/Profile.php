<?php
namespace App\Modules\Account;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    // model configuration
    protected $primaryKey = 'sid';
    public $timestamps = false;
    protected $dates = ['createdAt', 'modifiedAt', 'dob'];

    protected $guarded = ['sid', 'managedBy', 'createdBy', 'createdAt', 'modifiedBy'
        , 'modifiedAt', 'modifiedCounter'];

    /**
     * Get the user that owns the phone.
     */
    public function account()
    {
        return $this->belongsTo('App\Modules\Account\Account');
    }
}
