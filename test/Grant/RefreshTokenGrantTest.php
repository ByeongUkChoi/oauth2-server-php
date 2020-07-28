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
 * @testdox Refresh Token Grant Test
 * authrization code , access token, refresh token  issue test
 */
class RefreshTokenGrantTest extends TestCase
{
    private const NORMAL = 'NORMAL';
    private const ERROR = 'ERROR';
    /**
     * @testdox Refresh-token [Normal issuance] test
     */
    public function test_refresh_token_normal_issuance_test(): void
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
     * @testdox Refresh-token [Expiration] test
     */
    public function test_refresh_token_expiration_test(): void
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
     * @testdox Refresh-token [Deactivation] test
     */
    public function test_refresh_token_deactivation_test(): void
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
     * create RefreshTokenEntity
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
     * create AccessTokenEntity
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
     * create RefreshTokenRepositoryMock
     * 
     * For testing, set the expected value rather than accessing the DB directly to test..
     */
    private function createRefreshTokenRepositoryMock(RefreshTokenEntityInterface $refreshToken, string $testType): RefreshTokenRepositoryInterface
    {
        // refresh-token mock setup
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
     * create AccessTokenRepositoryMock
     * 
     * For testing, set the expected value rather than accessing the DB directly to test..
     */
    private function createAccessTokenRepositoryMock(AccessTokenEntityInterface $accessToken): AccessTokenRepositoryInterface
    {
        // access-token mock setup
        $accessTokenRepositoryMock = $this->createMock(AccessTokenRepositoryInterface::class);
        
        $accessTokenRepositoryMock
            ->method('getNewAccessToken')
            ->willReturn($accessToken);

        return $accessTokenRepositoryMock;
    }

    /**
     * create AuthorizationRequestDto
     */
    private function createRequest(string $clientId, string $clientSecret, string $redirectUri, string $code, string $refreshToken, string $grantType): AuthorizationRequestDto
    {
        $request = new AuthorizationRequestDto();
        $request->setClientId($clientId);
        $request->setClientSecret($clientSecret);
        $request->setRedirectUri($redirectUri);
        $request->setCode($code);
        $request->setRefreshToken($refreshToken);
        $request->setGrantType($grantType);

        return $request;
    }
}
