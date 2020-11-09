<?php
namespace Space48\ApiSecurity\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Webapi\Model\Authorization\TokenUserContext;
use Space48\ApiSecurity\Model\IpAuthenticationService;

class TokenUserContextPlugin
{
    /**
     * @var IpAuthenticationService
     */
    private $ipAuthenticationService;

    public function __construct(IpAuthenticationService $ipAuthenticationService)
    {
        $this->ipAuthenticationService = $ipAuthenticationService;
    }

    /**
     * @param TokenUserContext $tokenUserContext
     * @param $result
     * @return mixed
     * @throws AuthenticationException
     */
    public function afterGetUserType(TokenUserContext $tokenUserContext, $result)
    {
        if ($result === UserContextInterface::USER_TYPE_ADMIN) {
            $this->ipAuthenticationService->validateApiRequest();
        }

        return $result;
    }
}
