<?php

namespace oauth2serverTest\Grant;

use oauth2server\Grant\AuthorizationCodeGrant;
use oauth2server\Dto\AuthorizationRequestDto;
use oauth2server\Entity\AccessTokenEntityInterface;
use oauth2server\Entity\AuthorizationCodeEntityInterface;
use oauth2server\Entity\RefreshTokenEntityInterface;
use oauth2server\Repository\AccessTokenRepositoryInterface;
use oauth2server\Repository\AuthorizationCodeRepositoryInterface;
use oauth2server\Repository\RefreshTokenRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Authorization Code Grant 테스트
 */
class AuthorizationCodeGrantTest extends TestCase
{
    private const ACCESS_TOKEN = 'test-access-token';
    /**
     * @testdox 토큰 발급
     */
    public function testCreateToken(): void
    {
        $authorizationCodeEntity = $this->createMock(AuthorizationCodeEntityInterface::class);
        $authorizationCodeEntity
            ->method('getUsername')
            ->willReturn('test@example.com');

        $authorizationcodeRepository = $this->createMock(AuthorizationCodeRepositoryInterface::class);
        $authorizationcodeRepository
            ->method('findByCode')
            ->willReturn($authorizationCodeEntity);
        
        $accessTokenEntity = $this->createMock(AccessTokenEntityInterface::class);
        $accessTokenEntity
            ->method('getToken')
            ->willReturn(self::ACCESS_TOKEN);

        $accessTokenRepository = $this->createMock(AccessTokenRepositoryInterface::class);
        $accessTokenRepository
            ->method('getNewAccessToken')
            ->willReturn($accessTokenEntity);

        $refreshTokenEntity = $this->createMock(RefreshTokenEntityInterface::class);

        $refreshTokenRepository = $this->createMock(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository
            ->method('getNewRefreshToken')
            ->willReturn($refreshTokenEntity);

        $authorizationRequestDto = $this->createMock(AuthorizationRequestDto::class);
        $authorizationRequestDto
            ->method('getCode')
            ->willReturn('mock-code');

        $authorizationCodeGrant = new AuthorizationCodeGrant($authorizationcodeRepository, $accessTokenRepository, $refreshTokenRepository);
        $token = $authorizationCodeGrant->issueToken($authorizationRequestDto);
        $tokenToJson = json_encode($token);
        $tokenToObject = json_decode($tokenToJson);

        $this->assertEquals(self::ACCESS_TOKEN, $tokenToObject->access_token);
    }

}
