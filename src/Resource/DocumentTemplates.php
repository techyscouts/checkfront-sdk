<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class DocumentTemplates extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/document-templates', $query);
    }
}
