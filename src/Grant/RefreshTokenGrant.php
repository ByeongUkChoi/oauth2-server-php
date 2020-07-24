<?php 

namespace oauth2server\Grant;

use Exception;
use oauth2server\Dto\AuthorizationRequestDto;
use oauth2server\Dto\TokenDto;
use oauth2server\Repository\RefreshTokenRepositoryInterface;
use oauth2server\Repository\AccessTokenRepositoryInterface;

class RefreshTokenGrant implements GrantTypeInterface
{
    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;
    /**
     * @var RefreshTokenRepositoryInterface
     */
    private $refreshTokenRepository;

    public function __construct(
        AccessTokenRepositoryInterface $accessTokenRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    )
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }
    /**
     * 토큰 갱신
     * client_id, refresh_token을 검증하여 토큰 갱신 및 db 저장
     */
    public function issueToken(AuthorizationRequestDto $request): TokenDto
    {
        // refresh token 이 만료되었는지 확인
        $refreshToken = $this->refreshTokenRepository->findByRefreshToken($request->getRefreshToken());

        // refresh token 만료시 에러처리
        if($refreshToken->isExpired()) {
            // TODO: 에러 처리 변경 필요
            throw new Exception("refresh token is expired");
        }

        // refresh token의 use가 활성화 되어있지 않을 경우
        if( $refreshToken->isRevoked()) {
           // TODO: 에러 처리 변경 필요
           throw new Exception("refresh token is revoked"); 
        }

        // 만료되지 않았을 때 :
        // 기존 refresh token을 비활성화 시킨다.
        $this->refreshTokenRepository->revokeRefreshToken($request->getRefreshToken());

        $username = $refreshToken->getUsername();
        $clientId = $request->getClientId();

        $accessToken = $this->accessTokenRepository->getNewAccessToken($username);
        $accessTokenId = $accessToken->getToken();
        $refreshToken = $this->refreshTokenRepository->getNewRefreshToken($username, $clientId, $accessTokenId);

        // refresh tokens 테이블에 생성한다.
        $this->refreshTokenRepository->persistNewRefreshToken($refreshToken);
        
        return new TokenDto([
            'accessToken' => $accessTokenId,
            'expires' => $accessToken->getExpires(),
            'refreshToken' => $refreshToken->getToken(),
            'refreshTokenExpires' => $refreshToken->getExpires(),
        ]);
    }
}