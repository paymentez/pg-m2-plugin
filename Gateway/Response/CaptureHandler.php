<?php

namespace Paymentez\PaymentGateway\Gateway\Response;

use InvalidArgumentException;
use Magento\Framework\Validator\Exception as MagentoValidatorException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Helper\Logger;


class CaptureHandler implements HandlerInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * CaptureHandler constructor.
     * @param GatewayConfig $config
     */
    public function __construct(GatewayConfig $config)
    {
        $this->logger = $config->logger;
    }

    /**
     * @inheritDoc
     * @throws MagentoValidatorException
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }

        if (!isset($response['transaction']['status'])) {
            $this->logger->error(sprintf('CaptureHandler.handle $msg: response does not have status field'));
            throw new MagentoValidatorException(__('Sorry, your payment could not be processed. (Code: ERR01)'));
        }

        $status = $response['transaction']['status'];
        $transactionId = $response['transaction']['id'];
        $status_detail = $response['transaction']['status_detail'];

        if ($status !== 'success') {
            $rejected_msg = __('Sorry, your payment could not be processed. (Code: %1)', $status_detail);
            throw new MagentoValidatorException($rejected_msg);
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        /** @var Payment $payment */

        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(1);
    }
}
