<?php 

namespace oauth2server\Dto;

class AuthorizationRequestDto
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $grantType;

    public function __construct(array $data = null)
    {
        if(is_array($data)) {
            $this->fill($data);
        }
    }

    /**
     * 데이터 채우기
     */
    public function fill(array $data): void
    {
		foreach ($data as $key => $value)
		{
			$method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
    }


    /**
     * Get the value of clientId
     * @return  string
     */ 
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Set the value of clientId
     * @param  string  $clientId
     */ 
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * Get the value of clientSecret
     * @return  string
     */ 
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Set the value of clientSecret
     * @param  string  $clientSecret
     */ 
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get the value of redirectUri
     * @return  string
     */ 
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * Set the value of redirectUri
     * @param  string  $redirectUri
     */ 
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * Get the value of code
     * @return  string
     */ 
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set the value of code
     * @param  string  $code
     */ 
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get the value of refreshToken
     * @return  string
     */ 
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * Set the value of refreshToken
     * @param  string  $refreshToken
     */ 
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Get the value of grantType
     * @return  string
     */ 
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * Set the value of grantType
     * @param  string  $grantType
     */ 
    public function setGrantType(string $grantType): void
    {
        $this->grantType = $grantType;
    }
}