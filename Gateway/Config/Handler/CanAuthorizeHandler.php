<?php

namespace Paymentez\PaymentGateway\Gateway\Config\Handler;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Helper\Logger;

class CanAuthorizeHandler implements ValueHandlerInterface
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * CanAuthorizeHandler constructor.
     * @param GatewayConfig $config
     */
    public function __construct(GatewayConfig $config)
    {
        $this->logger = $config->logger;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $subject, $storeId = null)
    {
        $can_authorize = true;
        $this->logger->debug('CanAuthorizeHandler.handle $can_authorize' . $can_authorize);
        return $can_authorize;
    }
}
