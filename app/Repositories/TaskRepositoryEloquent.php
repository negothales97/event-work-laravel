<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class TaskRepositoryEloquent extends BaseRepository implements TaskRepository
{
    public function model()
    {
        return Task::class;
    }
}
