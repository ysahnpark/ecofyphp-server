<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Ecofy service
use App\Modules\Account\AccountServiceContract;

// Models
use App\Account;
use App\Auth;
use App\Profile;


class AccountApiController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, AccountServiceContract $accountService)
	{
        $queryCtx = new \stdClass();
        $queryCtx->envelop = $request->input('_meta', false);

		$records = $accountService->query(null);

		$result = null;
		if ($queryCtx->envelop) {
			$result = [
                'criteria' =>  'criteria',
                'page' => 'options.page',
                'offset' => 'options.offset',
                'limit' => 'options.limit'
			];
			$result['documents'] = $records->toArray();
			//$result['totalHits'] = $this->service->$countMethod($criteria);
		} else {
			$result = $records->toArray();
		}

		return json_encode($result, JSON_PRETTY_PRINT);
	}

	/**
	 * Showing the form is not supported
	 *
	 */
	public function create()
	{
		\App::abort(404);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = \Input::all();

		$createMethod = 'create' . $this->modelName;

        try {
        	$this->beforeRecordCreate($data);
            $record = $this->service->$createMethod($data);
            $this->afterRecordCreate($record);
            return \Response::json(array(
                'sid' => $record->sid),
                201
            );
        } catch (Exception $e) {
            return Response::json(array(
                'error' => $e->getMessage()),
                400
            );
        }
	}

	/**
	 * Return JSON representation of the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$record = Account::where('uuid', '=', $id)->first();

		//$record = $this->service->$findMethod($id);

		return $record;
	}

	/**
	 * Showing the form is not supported in API.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
	    \App::abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = \Input::all();

        $updateMethod = 'update' . $this->modelName;

        try {
			$this->beforeRecordUpdate($data);
            $record = $this->service->$updateMethod($id, $data);
            $this->afterRecordUpdate($record);
            return \Response::json(array(
                'sid' => $record->sid),
                200
            );
        } catch (Exception $e) {
            return \Response::json(array(
                'error' => $e->getMessage()),
                400
            );
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$deleteMethod = 'destroy' . $this->modelName;
		$result = $this->service->$deleteMethod($id);

		if (!empty($result)) {
			\Log::debug('Removing ' . $this->modelName . ': ' . $result->getName());
		} else {
			\Log::info($this->modelName .' record ' . $id . ' not found');
		}

		if (!empty($result)) {
		    return \Response::json(array(
                'removed' => $id),
                204
            );
		} else {
		    \App::abort(404);
		}
	}

}
