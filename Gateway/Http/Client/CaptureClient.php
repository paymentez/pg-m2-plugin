<?php

namespace Paymentez\PaymentGateway\Gateway\Http\Client;

use Magento\Sales\Model\Order\Payment;
use Paymentez\Paymentez;
use Paymentez\PaymentGateway\Gateway\Config\CardConfig;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Model\Adminhtml\Source\Currency;

class CaptureClient extends AbstractClient
{
    /**
     * CaptureClient constructor.
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
        $response = [];

        if (!isset($extra_data['transaction_id'])) {
            $this->logger->debug('CaptureClient.process Authorization is required...');
            /** @var Payment $payment */
            $payment = $request_body['objects']['payment'];
            $payment->authorize(1, $request_body['order']['amount']);
            $extra_data['transaction_id'] = $payment->getTransactionId();
        }

        if (Currency::validateForAuthorize($extra_data['currency'])) {
            $this->logger->debug('CaptureClient.process Consuming Capture...');
            $response = $charge->capture($extra_data['transaction_id']);
        } else {
            $this->logger->debug('CaptureClient.process Use mock for debited transactions...');
            $response = [
                'transaction' => [
                    'id' => $extra_data['transaction_id'],
                    'status' => 'success',
                    'status_detail' => 3
                ],
            ];
        }

        return (array)$response;
    }
}
