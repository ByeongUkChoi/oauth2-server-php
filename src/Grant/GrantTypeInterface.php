<?php 

namespace oauth2server\Grant;

use oauth2server\Dto\AuthorizationRequestDto;

interface GrantTypeInterface
{
    /**
     * 토큰 생성
     */
    public function issueToken(AuthorizationRequestDto $reqeustDto);
}