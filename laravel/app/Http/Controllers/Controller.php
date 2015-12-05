<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Ecofy\Support\SRQLParser;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
