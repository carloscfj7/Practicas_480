<?php

declare(strict_types=1);

namespace App\Usuarios\Application\Dto\Usuario\DataRequest;

class CredentialsDto
{
    public function __construct(public ?string $email = null, public ?string $password = null, public ?array $roles = null)
    {
    }
}