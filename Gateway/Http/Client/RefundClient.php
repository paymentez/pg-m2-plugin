<?php

namespace Paymentez\PaymentGateway\Gateway\Http\Client;

use Paymentez\Paymentez;
use Paymentez\PaymentGateway\Gateway\Config\CardConfig;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;

class RefundClient extends AbstractClient
{
    /**
     * RefundClient constructor.
     * @param Paymentez $adapter
     * @param GatewayConfig $gateway_config
     * @param CardConfig $config
     */
    public function __construct(Paymentez $adapter, GatewayConfig $gateway_config, CardConfig $config)
    {
        parent::__construct($adapter, $gateway_config);
        $this->config = $config;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function process(array $request_body)
    {
        $is_production = $this->config->isProduction();
        $credentials = $this->config->getServerCredentials();

        $this->adapter->init($credentials['application_code'], $credentials['application_key'], $is_production);

        $charge = $this->adapter::charge();
        $extra_data = $request_body['extra_data'];
        $amount = isset($extra_data['additional_amount']) ? $extra_data['additional_amount'] : $request_body['order']['amount'];

        $this->logger->debug('RefundClient.process Consuming Refund...');
        $response = $charge->refund($extra_data['transaction_id'], $amount);

        return (array)$response;
    }
}
