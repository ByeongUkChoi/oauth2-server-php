<?php

namespace oauth2server\Repository;

use oauth2server\Entity\AuthorizationCodeEntityInterface;

interface AuthorizationCodeRepositoryInterface
{
    // create new authorization code
    public function getNewAuthorizationCode(string $username, string $redirectUri): AuthorizationCodeEntityInterface;
    // insert datagbase authorization code
    public function persistNewAuthCode(AuthorizationCodeEntityInterface $authorizationCodeEntity): void;
    // get authorization code
    public function findByCode(string $code): AuthorizationCodeEntityInterface;
    // revoke authorization code
    public function revokeAuthorizationCode(string $code): void;
}