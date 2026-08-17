<?php

namespace App\Entity\Commons;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TokenAndRefreshTokenEntityRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

#[ORM\Entity(repositoryClass: TokenAndRefreshTokenEntityRepository::class)]
#[ORM\Table(name: "token_and_refresh_token_entity")]
class TokenAndRefreshTokenEntity extends RefreshToken
{

}
