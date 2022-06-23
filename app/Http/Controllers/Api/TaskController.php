<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Responses\DefaultResponse;
use App\Services\Params\Tasks\TaskStoreParams;
use App\Services\Contracts\TaskServiceInterface;

class TaskController extends ApiController
{
    protected $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }
    public function store(TaskStoreRequest $request)
    {
        $params = new TaskStoreParams(
            $request->title,
            $request->description,
            $request->priority,
            user('uuid'),
            company('uuid'),
            $request->admin_id ?? null
        );
        $taskResponse = $this->taskService->create($params);

        return $this->response(new DefaultResponse($taskResponse->data));
    }
}
