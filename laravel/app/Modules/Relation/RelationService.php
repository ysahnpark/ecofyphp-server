<?php

namespace App\Modules\Relation;

use DateTime;
use Log;
use DB;

use \Ramsey\Uuid\Uuid;
use \Firebase\JWT\JWT;

use App\Ecofy\Support\ObjectAccessor;
use App\Ecofy\Support\EcoCriteriaBuilder;
use App\Ecofy\Support\AbstractResourceService;

// Models
use App\Modules\Account\Account;
use App\Modules\Account\Profile;

use App\Modules\Relation\RelationServiceContract;


class RelationService extends AbstractResourceService
    implements RelationServiceContract
{
    protected $accountService = null;

    public function __construct() {
		parent::__construct('\\App\\Modules\\Relation\\Relation',['account']);
	}

    public function name()
    {
        return 'RelationService';
    }

    public function getAccountService()
    {
        if ( $this->accountService == null) {
            $this->accountService = \App::make('App\Modules\Account\AccountServiceContract');
        }
        return $this->accountService;
    }

    /**
     * Returns a new instance of account model
     */
    public function newRelation($array)
    {
        $model = new Relation($array);
        $model->createdAt = new DateTime();

        return $model;
    }

    /**
     * Request relation
     *
     * @param Model\Relation $Relation
     */
    public function request($requestor, $requestee, $relationhip)
    {

    }

    

}
