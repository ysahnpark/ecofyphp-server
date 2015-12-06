<?php
namespace App\Modules\Account;

use App\Ecofy\Support\AbstractResourceService;

// Models
use Illuminate\Database\Eloquent\Model;
use App\Modules\Auth\Auth;


class AccountService extends AbstractResourceService
    implements AccountServiceContract
{
    public function __construct() {
		parent::__construct('\\App\\Modules\\Account\\Account', ['profile']);
	}

    /**
     * Updates the lastLogin field to now
     */
	public function touchLastLogin($account)
	{
        $primaryKeyName = $this->primaryKeyName;
        $pk = $account->$primaryKeyName;
		$data = [
			'lastLogin' => new \DateTime()
		];
		$this->update($pk, $data);
	}

    // Resource Access Operations {{
    /**
     * Add
     *
     * @param Object  $resource - The resource (record) to add
     * @param Object  $options  - Any options for add operation
     * @return Model  - Upon success, return the added model
     */
    public function add($resource, $options = null)
    {
        $model = $resource;
        if ($resource instanceof Model) {
            $primaryKeyName = $this->primaryKeyName;
            if (empty($resoure->$primaryKeyName)) {
                $resoure->$primaryKeyName = $this->genUuid();
            }
            $resoure->save();
        } else {
            // Is an array
            if (empty($resoure['uuid'])) {
                $resoure['uuid'] = $this->genUuid();
            }
            $model = Account::create($resoure);
        }
        return $model;
    }

    /**
     * query
     *
     * @param Object  $criteria - The criteria for the query
     * @param Object  $options  - Any options for query operation
     * @return Array.<Model>  - Upon success, return the models
     */
     /*
    public function query($criteria, $options = null)
    {
        $result = Account::with('profile')->get();
        return $result;
    }
    */

    /**
     * Find
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
     /*
    public function find($criteria, $options = null)
    {
        $record = Account::where($criteria->property, $criteria->op, $criteria->val)->first();
        return $record;
    }
    */

    /**
     * Find by PK
     *
     * @param mixed  $pk - The primary key of the resource to find
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
     /*
    public function findByPK($pk, $options = null)
    {
        $record = Account::where($this->primaryKeyName, '=', $pk)->first();
        return $record;
    }
    */

    /**
     * Update
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $resource  - The resource (record) to update
     * @param object  $options  - Any options for update operation
     * @return Model  - Upon success the model returned
     */
     /*
    public function update($criteria, $resource, $options = null)
    {

    }
    */

    /**
     * Remove
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
     /*
    public function remove($criteria, $options = null)
    {
        $deletedRows = Account::where($criteria->property, $criteria->op, $criteria->val)->delete();
        return $deletedRows;
    }
    */

    /**
     * Remove
     *
     * @param mixed  $pk - The primary key of the resource to remove
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
     /*
    public function removeByPK($pk, $options = null)
    {
        $deletedRows = Account::where($this->primaryKeyName, '=', $pk)->delete();
        return $deletedRows;
    }
    */

    // }} Resource Access Operations
}
