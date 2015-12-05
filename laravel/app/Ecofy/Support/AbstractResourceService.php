<?php
namespace App\Ecofy\Support;

use Log;
use \Ramsey\Uuid\Uuid;


abstract class AbstractResourceService
{
    protected $queryBuilder;
    protected $modelFqn;
    protected $primaryKeyName = 'uuid';

    protected $eagerLoadRelations = null;

    /**
     * @param string $modelFqn  - Fully qualified name of the model
     * @param array $eagerLoadRelations -  Array of name of relations for the eager loading
     */
    public function __construct($modelFqn, $eagerLoadRelations = null)
    {
        $this->queryBuilder = new QueryBuilderEloquent();
        // @todo check that the modelFqn starts with '\'
    	$this->modelFqn = $modelFqn;

        $this->eagerLoadRelations = $eagerLoadRelations;
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
            if (empty($resoure[$this->primaryKeyName])) {
                $resoure[$this->primaryKeyName] = $this->genUuid();
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
        // Manual test: OK
        $query = $this->buildQuery($criteria);

        $offset = ObjectAccessor::get($options, 'offset', 0);
        $limit = ObjectAccessor::get($options, 'limit', 20);
        // [ <col> => <direction> ]
        $sortParams = ObjectAccessor::get($options, 'sortParams');

        $records = $query; //->skip($offset)->take($limit);
        if (!empty($sortParams)) {
            foreach($sortParams as $col => $direction ) {
                $query->orderBy($col, $direction);
            }
        }
        Log::debug('query: ' . $query->toSql());
        $records = $query->get();

        return $records;
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
        $query = $this->buildQuery($criteria);
        $count = $query->count();
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
        $query = $this->buildQuery($criteria);
        $record = $query->first();
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
        $criteria = $this->criteriaByPk($pk);
        $record = $this->find($criteria);
        return $record;
    }

    /**
     * Update
     *
     * @param mixed  $pk - The primary key of the resource to find
     * @param object  $resource  - The resource (record) to update
     * @param object  $options  - Any options for update operation
     * @return Model  - Upon success the model returned
     */
    public function update($pk, $resource, $options = null)
    {
        $criteria = $this->criteriaByPk($pk);
        $query = $this->buildQuery($criteria);
        return $query->update($resource->toArray());
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
        $query = $this->buildQuery($criteria);
        $deletedRows = $query->delete();
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
        $criteria = $this->criteriaByPk($pk);
        $deletedRows = $this->remove($criteria);
        return $deletedRows;
    }

    // }} Resource Access Operations

    public function genUuid()
    {
        return Uuid::uuid4();
    }

    public function criteriaByPk($pk)
    {
        return [
            'var' => $this->primaryKeyName,
            'op' => '=',
            'val' => $pk
        ];
    }

    public function buildQuery($criteria)
    {
        $modelClassName = $this->modelFqn;
        if (empty($criteria)) $criteria = array();
        $queryBuilder = new QueryBuilderEloquent();
        $modelQuery = $modelClassName::query();
        if ( !empty($this->eagerLoadRelations)) {
            $modelQuery->with($this->eagerLoadRelations);
        }
        $query = $modelQuery->getQuery();

        $queryBuilder->buildQuery($criteria, $query);

        return $modelQuery;
    }

}
