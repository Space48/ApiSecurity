<?php
namespace Space48\ApiSecurity\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class IpAuthenticationService
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $whitelistedIps;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(RemoteAddress $remoteAddress, ScopeConfigInterface $config)
    {
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
    }

    /**
     * @throws AuthenticationException
     */
    public function validateApiRequest(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if ($this->isCli()) {
            return;
        }

        $ipAddress = $this->remoteAddress->getRemoteAddress();

        if (!$this->isIpWhitelisted($ipAddress)) {
            throw new AuthenticationException(__('IP address not allowed'));
        }
    }

    private function isIpWhitelisted(string $ipAddress): bool
    {
        return in_array($ipAddress, $this->getWhitelistedIps(), true);
    }

    private function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    private function isEnabled(): bool
    {
        if ($this->enabled === null) {
            $this->enabled = $this->config->getValue('space48_apisecurity/ip_authentication/enabled') === '1';
        }
        return $this->enabled;
    }

    private function getWhitelistedIps(): array
    {
        if ($this->whitelistedIps === null) {
            $this->whitelistedIps = \array_filter(\array_map(
                static function (string $ipAddress) {
                    return trim($ipAddress);
                },
                explode("\n", $this->config->getValue('space48_apisecurity/ip_authentication/whitelisted_ips'))
            ));
        }
        return $this->whitelistedIps;
    }
}
