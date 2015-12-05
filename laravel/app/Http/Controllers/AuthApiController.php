<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;

use App\Ecofy\Support\AbstractResourceApiController;

use App\Modules\Auth\AuthServiceContract;

class AuthApiController extends AbstractResourceApiController
{
	public function __construct(AuthServiceContract $authService) {
		parent::__construct($authService);
	}

}
