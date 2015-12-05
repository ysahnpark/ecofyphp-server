<?php
namespace App\Ecofy\Support;

use DB;
use Log;
use Illuminate\Http\Request;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Ecofy\Support\SRQLParser;

abstract class AbstractResourceApiController extends BaseController
{
	protected $service = null;

	public function __construct($service) {
		$this->service = $service;
	}

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        $queryCtx = $this->queryContext($request);

		$records = $this->service->query($queryCtx->criteria, $queryCtx);

		$result = null;
		if ($queryCtx->envelop) {
			$result = [
                'criteria' => $queryCtx->q,
                'page' => $queryCtx->page,
                'offset' => $queryCtx->offset,
                'limit' => $queryCtx->limit
			];
			$result['documents'] = $records;
			$result['totalHits'] = $this->service->count($queryCtx->criteria);
		} else {
			$result = $records;
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
		$record = $this->service->findByPK($id);
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

        $updateMethod = 'update';

        try {
			//$this->beforeRecordUpdate($data);
            $record = $this->service->$updateMethod($id, $data);
            //$this->afterRecordUpdate($record);
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
		$deleteMethod = 'removeByPK';
		$result = $this->service->$deleteMethod($id);

		if (!empty($result)) {
			\Log::debug('Removed ' . $this->modelName . ': ' . $result->getName());
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


	public function queryContext($request)
    {
        $queryCtx = new \stdClass();
        $queryCtx->q = $request->input('q', false);
        $queryCtx->envelop = $request->input('_meta', false);
        $queryCtx->limit = $request->input('_limit');
        $queryCtx->offset = $request->input('_offset');
        $queryCtx->page = $request->input('_page');
        $queryCtx->criteria = null;

        if (!empty($queryCtx->q)) {
            $srqlParser = new SRQLParser();
            $qast = $srqlParser->parse($queryCtx->q);
            $queryCtx->criteria = $qast->text;
        }

        return $queryCtx;
    }

}