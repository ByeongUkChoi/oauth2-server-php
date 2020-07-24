<?php

namespace oauth2server\Entity;

interface AccessTokenEntityInterface
{
    // get Token
    public function getToken(): string;
    // get Expires (timestamp)
    public function getExpires(): int;
}