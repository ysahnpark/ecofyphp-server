<?php

namespace App\Modules\Account;


use App\Modules\Service\AbstractResourceService;

// Models
use Illuminate\Database\Eloquent\Model;
use App\Account;
use App\Auth;
use App\Profile;


class AccountService extends AbstractResourceService
    implements AccountServiceContract
{

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
    public function query($criteria, $options = null)
    {
        $result = Account::with('profile')->get();
        return $result;
    }

    /**
     * query
     *
     * @param Object  $criteria - The criteria for the query
     * @param Object  $options  - Any options for query operation
     * @return number  - Upon success, return count satisfying the criteria
     */
    public function count($criteria, $options = null)
    {
        $count = Account::where($criteria->property, $criteria->op, $criteria->val)->count();
        return $count;
    }

    /**
     * Find
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
    public function find($criteria, $options = null)
    {
        $record = Account::where($criteria->property, $criteria->op, $criteria->val)->first();
        return $record;
    }

    /**
     * Find by PK
     *
     * @param mixed  $pk - The primary key of the resource to find
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
    public function findByPK($pk, $options = null)
    {
        $record = Account::where($this->primaryKeyName, '=', $pk)->first();
        return $record;
    }

    /**
     * Update
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $resource  - The resource (record) to update
     * @param object  $options  - Any options for update operation
     * @return Model  - Upon success the model returned
     */
    public function update($criteria, $resource, $options = null)
    {

    }

    /**
     * Remove
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
    public function remove($criteria, $options = null)
    {
        $deletedRows = Account::where($criteria->property, $criteria->op, $criteria->val)->delete();
        return $deletedRows;
    }

    /**
     * Remove
     *
     * @param mixed  $pk - The primary key of the resource to remove
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
    public function removeByPK($pk, $options = null)
    {
        $deletedRows = Account::where($this->primaryKeyName, '=', $pk)->delete();
        return $deletedRows;
    }

    // }} Resource Access Operations
}
