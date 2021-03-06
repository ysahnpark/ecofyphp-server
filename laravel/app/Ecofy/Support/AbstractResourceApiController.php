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

abstract class AbstractResourceApiController extends AbstractResourceController
{

	public function __construct($service) {
		parent::__construct($service);
	}

    /**
	 * Display a listing of the resource.
	 *
	 * @param {Requeest} $request
	 * @return Response
	 */
	public function index(Request $request)
	{
        $queryCtx = $this->queryContext($request);

		$resources = $this->service->query($queryCtx->criteria, $queryCtx);

		$result = null;
		if ($queryCtx->envelop) {
			$result = [
                'criteria' => $queryCtx->q,
                'page' => $queryCtx->page,
                'offset' => $queryCtx->offset,
                'limit' => $queryCtx->limit
			];
			$result['documents'] = $resources;
			$result['totalHits'] = $this->service->count($queryCtx->criteria);
		} else {
			$result = $resources;
		}

		//var_dump($result);
		//die();

		return json_encode($result, JSON_PRETTY_PRINT);
	}

	/**
	 * Showing the form is not supported
	 *
	 */
	public function create()
	{
		return $this->jsonResponse('Unsupported endpoint', 404);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = \Input::all();

		$createMethod = 'add';

        try {
        	$this->beforeResourceCreate($data);
            $resource = $this->service->$createMethod($data);
            $this->afterResourceCreate($resource);

			return $this->jsonResponse(array('added' => $resource->uuid), 201);
        } catch (Exception $e) {
			// @todo set the status code accordingly
            return $this->jsonResponse(array('error' => $e->getMessage()), 500);
        }
	}

	/**
	 * Return JSON representation of the specified resource.
	 *
	 * @param  mixed  $id
	 * @return Response
	 */
	public function show($id)
	{
		$resource = $this->service->findByPK($id);

		if (!empty($resource)) {
			return $this->jsonResponse($resource, 200);
		} else {
			return $this->jsonResponse('Record not found: ' . $id, 404);
		}
	}

	/**
	 * Showing the form is not supported in API.
	 *
	 * @param  mixed  $id
	 * @return Response
	 */
	public function edit($id)
	{
	    return $this->jsonResponse('Unsupported endpoint', 404);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  mixed  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = \Input::all();

        $updateMethod = 'update';

        try {
			$this->beforeResourceUpdate($data);
            $resource = $this->service->$updateMethod($id, $data);
            $this->afterResourceUpdate($resource);

			return $this->jsonResponse(array('updated' => $id), 200);

        } catch (Exception $e) {
			return $this->jsonResponse(array('error' => $e->getMessage()), 500);
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
			\Log::debug('Removed ' . $this->service->getModelFqn() . ': ' . $id);
		} else {
			\Log::info('Record ' . $id . ' not found');
		}

		if (!empty($result)) {
			return $this->jsonResponse(array('removed' => $id), 200);
		} else {
			return $this->jsonResponse('Record not found: ' . $id, 404);
		}
	}


}
