<?php

namespace Paymentez\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\Exception as MagentoValidatorException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Paymentez\PaymentGateway\Gateway\Config\CardConfig;
use Paymentez\PaymentGateway\Gateway\Config\LinkToPayConfig;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Helper\Logger;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * DataAssignObserver constructor.
     * @param GatewayConfig $config
     */
    public function __construct(GatewayConfig $config)
    {
        $this->logger = $config->logger;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);
        $paymentInfo = $method->getInfoInstance();
        $additional_data = $data->getDataByKey('additional_data');
        switch ($data->getDataByKey('method')) {

            case CardConfig::CODE:
                $installment = isset($additional_data['installment']) ? $additional_data['installment'] : 1;
                $installment_type = isset($additional_data['installment_type']) ? $additional_data['installment_type'] : -1;

                $token = isset($additional_data['token']) ? $additional_data['token'] : null;

                $paymentInfo->setAdditionalInformation('installment', $installment);
                $paymentInfo->setAdditionalInformation('installment_type', $installment_type);
                $paymentInfo->setAdditionalInformation('token', $token);
                break;

            //  Add here more payment methods as: LTP, Cash, PSE
            case LinkToPayConfig::CODE:
                $installment = isset($additional_data['installment']) ? $additional_data['installment'] : 1;

                $paymentInfo->setAdditionalInformation('installment', $installment);
                break;
        }
        $this->logger->debug(sprintf('DataAssignObserver.execute $paymentInfo:'), (array)$paymentInfo);

    }
}
