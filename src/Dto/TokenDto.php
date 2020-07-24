<?php 

namespace oauth2server\Dto;

use JsonSerializable;

class TokenDto implements JsonSerializable
{
    private $accessToken;
    private $expires;
    private $refreshToken;
    private $refreshTokenExpires;

    public function __construct(array $data)
    {
		foreach ($data as $key => $value)
		{
			$this->$key = $value;
		}
    }

    public function jsonSerialize(): array
    {
        return [
            'access_token' => $this->accessToken,
            'expires' => $this->expires,
            'refresh_token' => $this->refreshToken,
            'refresh_token_expires' => $this->refreshTokenExpires
        ];
    }
}