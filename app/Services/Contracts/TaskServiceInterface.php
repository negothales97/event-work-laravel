<?php

namespace App\Services\Contracts;

use App\Services\Params\Tasks\TaskStoreParams;
use App\Services\Responses\ServiceResponse;

interface TaskServiceInterface
{
    public function create(TaskStoreParams $params): ServiceResponse;
}
