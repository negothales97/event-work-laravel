<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepository;
use App\Services\Contracts\TaskServiceInterface;
use App\Services\Params\Tasks\TaskStoreParams;
use App\Services\Responses\ServiceResponse;

class TaskService extends BaseService implements TaskServiceInterface
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Adiciona task
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function create(TaskStoreParams $params): ServiceResponse
    {
        try {
            $task = $this->taskRepository->create($params->toArray());
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, $params);
        }
        return new ServiceResponse(
            true,
            'Task adicionada',
            $task
        );
    }
}
