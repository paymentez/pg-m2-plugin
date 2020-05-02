<?php

namespace Paymentez\PaymentGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Paymentez\PaymentGateway\Gateway\Config\CardConfig;
use Paymentez\PaymentGateway\Helper\Logger;

class CountryValidator extends AbstractValidator
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var CardConfig
     */
    private $config;

    public function __construct(ResultInterfaceFactory $resultFactory)
    {
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $country = $validationSubject['country'];
        $supported_countries = $this->config->getSupportedCountries();
        $isValid = in_array($country, $supported_countries);

        return $this->createResult($isValid);
    }
}
