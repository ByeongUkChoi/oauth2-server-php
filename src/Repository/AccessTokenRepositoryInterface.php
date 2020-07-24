<?php

namespace oauth2server\Repository;

use oauth2server\Entity\AccessTokenEntityInterface;

interface AccessTokenRepositoryInterface
{
    /**
     * create new access token
     */
    public function getNewAccessToken(string $username): AccessTokenEntityInterface;
    /**
     * insert database access token
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessToken): void;
}