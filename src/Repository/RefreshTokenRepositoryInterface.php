<?php

namespace oauth2server\Repository;

use oauth2server\Entity\RefreshTokenEntityInterface;

interface RefreshTokenRepositoryInterface
{
    /**
     * get refresh token
     */
    public function findByRefreshToken(string $tokne): RefreshTokenEntityInterface;
    /**
     * create new refresh token
     */
    public function getNewRefreshToken(string $username, string $cleintId, string $accessTokenId): RefreshTokenEntityInterface;
    /**
     * insert database refresh token
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken): void;
    /**
     * revoke refresh token
     */
    public function revokeRefreshToken(string $token): void;
}