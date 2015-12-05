<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;

use App\Ecofy\Support\AbstractResourceApiController;

// Ecofy service
use App\Modules\Account\AccountServiceContract;

class AccountApiController extends AbstractResourceApiController
{
	protected $service = null;

	public function __construct(AccountServiceContract $accountService) {
		$this->service = $accountService;
	}

}
