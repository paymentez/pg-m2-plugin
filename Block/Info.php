<?php

namespace Paymentez\PaymentGateway\Block;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;
use Paymentez\PaymentGateway\Gateway\Response\FraudHandler;
use Paymentez\PaymentGateway\Helper\Logger;

class Info extends ConfigurableInfo
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * Info constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param Logger $logger
     * @param array $data
     */
    public function __construct(Context $context, ConfigInterface $config, Logger $logger, array $data = [])
    {
        parent::__construct($context, $config, $data);
        $this->logger = $logger;
    }

    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        $this->logger->debug(sprintf('Info.getLabel => %s', $field));
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     */
    protected function getValueView($field, $value)
    {
        switch ($field) {
            case FraudHandler::FRAUD_MSG_LIST:
                $this->logger->debug(sprintf('Info.getValueView => %s', implode('; ', $value)));
                return implode('; ', $value);
        }
        $this->logger->debug(sprintf('Info.getValueView => %s - %s', $field, $value));
        return parent::getValueView($field, $value);
    }

}
