<?php

namespace App\Services\Params\Tasks;

class TaskStoreParams extends BaseServiceParams
{
    public $title;
    public $description;
    public $priority;
    public $status;
    public $admin_id;
    public $user_id;
    public $company_id;

    public function __construct(
        $title,
        $description,
        $priority,
        $user_id,
        $company_id,
        $status = 'open',
        $admin_id = null
    ) {
        parent::__construct();
    }
}
