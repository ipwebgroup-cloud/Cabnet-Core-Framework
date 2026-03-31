<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

use Cabnet\Application\Crud\Definitions\ServiceEntityDefinition;
use Cabnet\Infrastructure\Repositories\ServiceRepository;
use Cabnet\Support\UploadManager;

class ServiceCrudService extends DefinitionCrudService
{
    public function __construct(
        ServiceRepository $repository,
        \Validator $validator,
        mixed $db = null,
        ?UploadManager $uploadManager = null
    ) {
        parent::__construct(ServiceEntityDefinition::make(), $repository, $validator, $db, $uploadManager);
    }
}
