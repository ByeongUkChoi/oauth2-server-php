<?php

namespace oauth2serverTest\Grant;

use Exception;
use oauth2server\Dto\AuthorizationRequestDto;
use oauth2server\Entity\AccessTokenEntityInterface;
use oauth2server\Entity\RefreshTokenEntityInterface;
use oauth2server\Grant\RefreshTokenGrant;
use oauth2server\Repository\AccessTokenRepositoryInterface;
use oauth2server\Repository\RefreshTokenRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @testdox OAuth2 테스트
 * authrization code , access token, refresh token 발급 테스트
 */
class RefreshTokenGrantTest extends TestCase
{
    private const NORMAL = 'NORMAL';
    private const ERROR = 'ERROR';
    /**
     * @testdox refresh token 정상 발급 테스트
     */
    public function test_refresh_token_정상발급(): void
    {
        $refreshToken = $this->createRefreshTokenEntityMock('refresh-token', 'test@example.com', 'access-token', false, false);
        $refreshTokenRepositoryMock = $this->createRefreshTokenRepositoryMock($refreshToken, self::NORMAL);
        
        $accessToken = $this->createAccessTokenEntityMock('access-token');
        $accessTokenRepositoryMock = $this->createAccessTokenRepositoryMock($accessToken);

        $request = $this->createRequest('test-client', '', '', '', 'refresh-token', '');

        $refreshTokenGrant = new RefreshTokenGrant($accessTokenRepositoryMock, $refreshTokenRepositoryMock);

        $token = $refreshTokenGrant->issueToken($request);
        $tokenToJson = json_encode($token);
        $tokenToObject = json_decode($tokenToJson);

        $this->assertEquals('refresh-token', $tokenToObject->refresh_token);
        $this->assertEquals('access-token', $tokenToObject->access_token);
    }

    /**
     * @testdox refresh token 기한 만료 테스트
     */
    public function test_refresh_token_기한_만료_테스트(): void
    {
        $refreshToken = $this->createRefreshTokenEntityMock('refresh-token', 'test@example.com', 'access-token', false, true);
        $refreshTokenRepositoryMock = $this->createRefreshTokenRepositoryMock($refreshToken, self::ERROR);
        
        $accessToken = $this->createAccessTokenEntityMock('access-token');
        $accessTokenRepositoryMock = $this->createAccessTokenRepositoryMock($accessToken);

        $request = $this->createRequest('test-client', '', '', '', 'refresh-token', '');

        $refreshTokenGrant = new RefreshTokenGrant($accessTokenRepositoryMock, $refreshTokenRepositoryMock);

        $this->expectException(Exception::class);
        $refreshTokenGrant->issueToken($request);
    }

    /**
     * @testdox refresh token 비활성화 테스트
     */
    public function test_refresh_token_비활성화_테스트(): void
    {
        $refreshToken = $this->createRefreshTokenEntityMock('refresh-token', 'test@example.com', 'access-token', true, false);
        $refreshTokenRepositoryMock = $this->createRefreshTokenRepositoryMock($refreshToken, self::ERROR);
        
        $accessToken = $this->createAccessTokenEntityMock('access-token');
        $accessTokenRepositoryMock = $this->createAccessTokenRepositoryMock($accessToken);

        $request = $this->createRequest('test-client', '', '', '', 'refresh-token', '');

        $refreshTokenGrant = new RefreshTokenGrant($accessTokenRepositoryMock, $refreshTokenRepositoryMock);

        $this->expectException(Exception::class);
        $refreshTokenGrant->issueToken($request);
    }

    /**
     * RefreshTokenEntity를 생성
     */
    private function createRefreshTokenEntityMock(string $id, string $username, string $accessToken, bool $isRevoked, bool $isExpired): RefreshTokenEntityInterface
    {
        $refreshToken = $this->createMock(RefreshTokenEntityInterface::class);
        $refreshToken
            ->method('getToken')
            ->willReturn($id);
        $refreshToken
            ->method('getUsername')
            ->willReturn($username);
        $refreshToken
            ->method('getAccessToken')
            ->willReturn($accessToken);
        $refreshToken
            ->method('isRevoked')
            ->willReturn($isRevoked);
        $refreshToken
            ->method('isExpired')
            ->willReturn($isExpired);

        return $refreshToken;
    } 

    /**
     * AccessTokenEntity를 생성
     */
    private function createAccessTokenEntityMock(string $id): AccessTokenEntityInterface
    {
        $accessToken = $this->createMock(AccessTokenEntityInterface::class);
        $accessToken
            ->method('getToken')
            ->willReturn($id);

        return $accessToken;
    }

    /**
     * RefreshTokenRepository 객체를 Mock 객체로 생성
     * 
     * 테스트용으로 직접 DB에 접근하는 것이 아닌 기대값을 설정하여 테스트 할 수 있도록 한다.
     */
    private function createRefreshTokenRepositoryMock(RefreshTokenEntityInterface $refreshToken, string $testType): RefreshTokenRepositoryInterface
    {
        // refresh token mock 설정
        $refreshTokenRepositoryMock = $this->createMock(RefreshTokenRepositoryInterface::class);

        $refreshTokenRepositoryMock
            ->method('findByRefreshToken')
            ->willReturn($refreshToken);

        switch($testType) {
            case self::NORMAL:
                $refreshTokenRepositoryMock
                    ->method('getNewRefreshToken')
                    ->willReturn($refreshToken);
                break;
            case self::ERROR:
                break;
            default :
                // error code ...
                break;
        }

        return $refreshTokenRepositoryMock;
    }

    /**
     * AccessTokenRepository 객체를 Mock 객체로 생성 
     * 
     * 테스트용으로 직접 DB에 접근하는 것이 아닌 기대값을 설정하여 테스트 할 수 있도록 한다. 
     */
    private function createAccessTokenRepositoryMock(AccessTokenEntityInterface $accessToken): AccessTokenRepositoryInterface
    {
        // access token mock 설정
        $accessTokenRepositoryMock = $this->createMock(AccessTokenRepositoryInterface::class);
        
        $accessTokenRepositoryMock
            ->method('getNewAccessToken')
            ->willReturn($accessToken);

        return $accessTokenRepositoryMock;
    }

    /**
     * AuthorizationRequestDto를 생성.
     */
    private function createRequest(string $clientId, string $clientSecret, string $redirectUri, string $code, string $refreshToken, string $grantType): AuthorizationRequestDto
    {
        $request = new AuthorizationRequestDto();
        // TODO : request 정의하기
        $request->setClientId($clientId);
        $request->setClientSecret($clientSecret);
        $request->setRedirectUri($redirectUri);
        $request->setCode($code);
        $request->setRefreshToken($refreshToken);
        $request->setGrantType($grantType);

        return $request;
    }
}
