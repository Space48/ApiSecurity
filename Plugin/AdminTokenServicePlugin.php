<?php
namespace Space48\ApiSecurity\Plugin;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Space48\ApiSecurity\Model\IpAuthenticationService;

class AdminTokenServicePlugin
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
     * @param AdminTokenServiceInterface $subject
     * @param $username
     * @param $password
     * @return array
     * @throws AuthenticationException
     */
    public function beforeCreateAdminAccessToken(
        AdminTokenServiceInterface $subject,
        $username,
        $password
    ) {
        $this->ipAuthenticationService->validateApiRequest();

        return [$username, $password];
    }
}
