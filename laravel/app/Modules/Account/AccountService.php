<?php
namespace App\Modules\Account;

use DateTime;
use DB;
use Log;

use App\Ecofy\Support\EcoCriteriaBuilder;
use App\Ecofy\Support\AbstractResourceService;

// Models
use Illuminate\Database\Eloquent\Model;
use App\Modules\Auth\Auth;


class AccountService extends AbstractResourceService
    implements AccountServiceContract
{
    public function __construct() {
		parent::__construct('\\App\\Modules\\Account\\Account', ['profile', 'auths']);
	}

    public function newAccount($array)
    {
        $model = new Account($array);
        $model->createdAt = new DateTime();

        return $model;
    }

    public function newProfile($array)
    {
        $model = new Profile($array);
        $model->createdAt = new DateTime();

        return $model;
    }

    // Override
    /*
    public function sanitizeData($data, $modelFqn = NULL)
    {
        //unset($data['lastLogin']);
        return $this->sanitizeData($data);
    }*/

    /**
     * Sanitize the Profile data
     * @return Profile model
     */
    public function sanitizeProfileData($data)
    {
        return $this->sanitizeData($data, Profile::class);
        /*
        $model = new Profile();
        $dob = DateTime::createFromFormat('Y-m-d+', $data['dob']);
        // @todo - remove the time part
        $data['dob'] = $model->fromDateTime($dob);
        $model->fill($data);
        return $model->toArray();
        */
    }

    /**
     * find by email
     *
     * @param string  $email - Email to search for
     * @return Model  - Upon success, return the found model
     */
    public function findByEmail($email)
    {
        $criteria = EcoCriteriaBuilder::comparison('primaryEmail', '=', $email);
        return $this->find($criteria);
    }

    /**
     * Updates the lastLogin field to now
     * @param Model\Account $account  - The account to update
     */
	public function touchLastLogin($account)
	{
        $primaryKeyName = $this->primaryKeyName;
        $pk = $account->$primaryKeyName;
		$data = [
			//'lastLogin' => new \DateTime()
            'lastLogin' => new \Carbon\Carbon()
		];
		$this->update($pk, $data);
	}

    // Overriding from AbstractResourceService
    // Resource Access Operations {{

    /**
     * Updates the record.
     * Wihin a transaction update profile and account
     */
    public function update($pk, $data, $options = null)
    {
        $profileData = null;
        if (array_key_exists('profile', $data)) {
            $profileData = $this->sanitizeProfileData($data['profile']);

            //var_dump($profileData);
            //die();

            unset($data['profile']);
        }
        unset($data['auths']);

        $updateResult = null;
        DB::transaction(function () use($pk, $data, $profileData) {
            if (!empty($profileData)) {
                // Update profile
                $query = Profile::query()->getQuery();
                $criteria = EcoCriteriaBuilder::comparison('accountUuid', '=', $pk);

                $this->buildQuery($criteria, Profile::class)->update($profileData);
            }

            // Update account
            $updateResult = parent::update($pk, $data);
        });

        return $updateResult;
    }

    // }  Resource Access Operations


}
