<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Ecofy\Util\CsvUtil;

class ImportApiController extends Controller
{

	/**
	 * Array of {name, title, model_class_name, model_name, importStrategy}
	 */
	protected $importables = array();

	/**
	 * Constructor
	 */
	public function __construct()
    {
		$this->addImportable('account', Accounts::class, 'App\Modules\Account\AccountService');
    }

    public function addImportable($name, $modelFqn, $serviceFqn)
    {
    	$this->importables[$name] = array(
	    		'model_fqn' => $modelFqn,
                'service_fqn' => $serviceFqn,
    		);
    }

	public function process() {
        $payload = \Input::json();
        $mode = $payload->get('mode'); // preview | process
		$type = $payload->get('type');
        $data = $payload->get('data');
        $onmatch = $payload->get('onmatch');

		$updateOnMatch = ($onmatch == 'update');

		$result = $this->processRecords($type, $mode, $updateOnMatch, $data);

		return $result;
	}

	/**
	 * Validates or Processes the records
     * @param type {string} - The model type, e.g. "account"
	 */
	private function processRecords($type, $mode, $updateOnMatch, &$rows)
	{
		$result = array(
			'errors' => array(),
			'items_count' => 0,
			'items_created_count' => 0,
			'items_updated_count' => 0,
			'items_skipped_count' => 0,
			'items_created' => [],
			'items_updated' => [],
			'items_skipped' => []
			);
		$errors = array();
		$importable = $this->importables[$type];
		$modelFqn = '\\' . $importable['model_fqn'];
        $service = \App::make($importable['service_fqn']);

		//$importStrategy = $importable['strategy'];

		$linenum = 0;
		$prev_row = null;

		$sessionAccount = \Auth::user();
		foreach ($rows as &$row)
        {
			$linenum++;

			//if ($importStrategy !== null)
			//	$importStrategy->prepareInput($row, $prev_row);
			$validator = $service->validator($row);
			$prev_row = $row;

            //print_r($row);
	        if (!$validator->passes()) {
	        	$errors[] = array('line' => $linenum, 'message' => $validator->messages()->toArray());
			} else {
				if ($mode == 'import') {
					try {

						$service->prepareRecordForImport($row);
						$model = $service->createNewModel($row);
						$model->createdBy = $sessionAccount->uuid;
						//print_r($model->toJson(JSON_PRETTY_PRINT));
						//die();

						$record = $service->add($model);

						$result['items_created'][] = $record->_name();
						$result['items_created_count']++;

					} catch (Exception $ex) {
						$errors[] = array('line' => $linenum, 'message' => $ex->getMessage());
					}
				}
			}
		}
		$result['errors'] = $errors;
		$result['items_count'] = count($rows);

		return $result;
	}


}
