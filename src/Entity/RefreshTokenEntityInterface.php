<?php

namespace oauth2server\Entity;

interface RefreshTokenEntityInterface
{
    // get Token
    public function getToken(): string;
    // get Expires (timestamp)
    public function getExpires(): int;
    // check token expired
    public function isExpired(): bool;
    // use가 활성화 되어있는지 여부
    public function isRevoked(): bool;
    // accessToken 값을 가져옴
    public function getAccessToken(): string;
    // get Username
    public function getUsername(): string;
}