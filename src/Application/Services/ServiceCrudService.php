<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

use Cabnet\Application\Crud\Definitions\ServiceEntityDefinition;
use Cabnet\Infrastructure\Repositories\ServiceRepository;

class ServiceCrudService extends DefinitionCrudService
{
    public function __construct(
        ServiceRepository $repository,
        \Validator $validator
    ) {
        parent::__construct(ServiceEntityDefinition::make(), $repository, $validator);
    }
}
