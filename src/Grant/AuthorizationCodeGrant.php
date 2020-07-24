<?php

namespace oauth2server\Grant;

use Exception;
use oauth2server\Dto\AuthorizationRequestDto;
use oauth2server\Dto\TokenDto;
use oauth2server\Repository\AccessTokenRepositoryInterface;
use oauth2server\Repository\AuthorizationCodeRepositoryInterface;
use oauth2server\Repository\RefreshTokenRepositoryInterface;
use TheSeer\Tokenizer\Token;

class AuthorizationCodeGrant implements GrantTypeInterface
{
    /**
     * @var AuthorizationCodeRepositoryInterface
     */
    private $authorizationCodeRepository;
    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;
    /**
     * @var RefreshTokenRepositoryInterface
     */
    private $refreshTokenRepository;

    public function __construct(
        AuthorizationCodeRepositoryInterface $authorizationCodeRepository,
        AccessTokenRepositoryInterface $accessTokenRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    )
    {
        $this->authorizationCodeRepository = $authorizationCodeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }
    /**
     * 토큰 생성
     * code, clientId, redirectUri를 검증하여 토큰 발급 및 db 저장
     */
    public function issueToken(AuthorizationRequestDto $authorizationRequestDto): TokenDto
    {
        $code = $authorizationRequestDto->getCode();
        $authorizationCode = $this->authorizationCodeRepository->findByCode($code);
        if($authorizationCode->isExpired()) {
            throw new Exception("authorization code is expired");
        }
        $username = $authorizationCode->getUsername(); 
        $clientId = $authorizationRequestDto->getClientId();

        $accessToken = $this->accessTokenRepository->getNewAccessToken($username);
        $accessTokenId = $accessToken->getToken();
        $refreshToken = $this->refreshTokenRepository->getNewRefreshToken($username, $clientId, $accessTokenId);

        // db insert
        $this->refreshTokenRepository->persistNewRefreshToken($refreshToken);
        $this->accessTokenRepository->persistNewAccessToken($accessToken);

        // revoke authorization code
        $this->authorizationCodeRepository->revokeAuthorizationCode($code);

        return new TokenDto([
            'accessToken' => $accessTokenId,
            'expires' => $accessToken->getExpires(),
            'refreshToken' => $refreshToken->getToken(),
            'refreshTokenExpires' => $refreshToken->getExpires(),
        ]);
    }
}