<?php

namespace Paymentez\PaymentGateway\Gateway\Response;

use Exception;
use InvalidArgumentException;
use Magento\Framework\Validator\Exception as MagentoValidatorException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;
use Paymentez\PaymentGateway\Helper\Logger;
use Paymentez\PaymentGateway\Helper\UtilManagement;


class AuthorizeHandler implements HandlerInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * AuthorizeHandler constructor.
     * @param GatewayConfig $config
     */
    public function __construct(GatewayConfig $config)
    {
        $this->logger = $config->logger;
    }

    /**
     * @inheritDoc
     * @throws MagentoValidatorException
     * @throws Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }

        if (!isset($response['transaction']['status'])) {
            $this->logger->error(sprintf('AuthorizeHandler.handle $msg: response does not have status field'));
            throw new MagentoValidatorException(__('Sorry, your payment could not be processed. (Code: ERR01)'));
        }

        $status = $response['transaction']['status'];
        $transaction_id = $response['transaction']['id'];
        $status_detail = $response['transaction']['status_detail'];

        if ($status == 'failure') {
            $rejected_msg = __('Sorry, your payment could not be processed. (Code: %1)', $status_detail);
            throw new MagentoValidatorException($rejected_msg);
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();

        $payment->setTransactionId($transaction_id);
        $payment->setIsTransactionClosed(0);

        // TODO: Review by Kount is status_detail 1
        if ($status_detail == 1) {
            UtilManagement::setStatusForReviewByKount($paymentDO, $this->logger, true);
        }
    }
}
