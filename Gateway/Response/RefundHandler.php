<?php

namespace Paymentez\PaymentGateway\Gateway\Response;

use InvalidArgumentException;
use Magento\Framework\Validator\Exception as MagentoValidatorException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Helper\Logger;


class RefundHandler implements HandlerInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * RefundHandler constructor.
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
            $this->logger->error(sprintf('RefundHandler.handle $msg: response does not have status field'));
            throw new MagentoValidatorException(__('Sorry, your payment could not be processed. (Code: ERR01)'));
        }

        $status = $response['status'];
        $detail = $response['detail'];
        $transactionId = $response['transaction']['id'];
        $amount = $response['transaction']['refund_amount'];

        if ($status == 'failure') {
            $rejected_msg = __('Sorry, your refund could not be processed. (Code: %1)', $detail);
            throw new MagentoValidatorException($rejected_msg);
        }
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        /** @var Payment $payment */


        $payment->setAmountCanceled($amount);
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(1);
        $payment->setShouldCloseParentTransaction(1);
        $this->logger->debug(sprintf('RefundHandler.handle Closed transaction: %s', $transactionId));
    }
}
