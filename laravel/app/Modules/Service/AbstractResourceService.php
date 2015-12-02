<?php

namespace App\Modules\Service;

use \Ramsey\Uuid\Uuid;


abstract class AbstractResourceService
{
    protected $primaryKeyName = 'uuid';

    public function genUuid()
    {
        return Uuid::uuid4();
    }
}
