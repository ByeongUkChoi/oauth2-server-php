<?php

namespace oauth2server\Entity;

interface AuthorizationCodeEntityInterface
{
    // get Code
    public function getCode(): string;
    // get Username
    public function getUsername(): string;
    // check code expired
    public function isExpired(): bool;
}