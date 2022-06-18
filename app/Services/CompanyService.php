<?php

namespace App\Services;

use App\Repositories\Contracts\CompanyRepository;
use App\Services\Contracts\CompanyServiceInterface;
use App\Services\Responses\ServiceResponse;
use Illuminate\Support\Facades\DB;

class CompanyService extends BaseService implements CompanyServiceInterface
{
    /**
     * @var UserRepository
     */
    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }
    /**
     * Create an user in database
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function create(array $data): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $company = $this->companyRepository->create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, $data);
        }
        DB::commit();

        return new ServiceResponse(
            true,
            'Empresa criada com sucesso',
            $company
        );
    }
}
