<?php
namespace Paymentez\PaymentGateway\Model;

use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Framework\Webapi\Exception;

use Paymentez\PaymentGateway\Api\WebhookInterface;
use Paymentez\PaymentGateway\Helper\Logger;
use Paymentez\PaymentGateway\Gateway\Config\GatewayConfig;


class OrderWebhook implements WebhookInterface
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
    * @var OrderInterface
    */
    protected $order;
    /**
    * @var GatewayConfig
    */
    protected $config;

    /**
    * OrderWebhook constructor.
    * @param Logger $logger
    * @param RequestInterface $request
    * @param OrderInterface $order
    * @param GatewayConfig $config
    */
    public function __construct(Logger $logger, OrderInterface $order, GatewayConfig $config) {
        $this->order  = $order;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
    * Method that manages the update order via webhook.
    * @return Exception
    */
    public function updateOrderWebhook() {
      $params           = json_decode(file_get_contents('php://input'), true);
      $status           = $params["transaction"]['status'];
      $status_detail    = (int)$params["transaction"]['status_detail'];
      $transaction_id   = $params["transaction"]['id'];
      $dev_reference    = $params["transaction"]['dev_reference'];
      $pg_stoken        = $params["transaction"]['stoken'];
      $application_code = $params["transaction"]['application_code'];
      $auth_code        = $params["transaction"]['authorization_code'];
      $message          = $params["transaction"]['message'];
      $carrier_code     = $params["transaction"]['carrier_code'];
      $amount           = (float)$params["transaction"]['amount'];
      $user_id          = $params["user"]['id'];

      $this->validateStoken($user_id, $transaction_id, $application_code, $pg_stoken, $this->config);

      $order = $this->order->loadByIncrementId($dev_reference);
      if (!$order->getId()) {
          throw new Exception(__('Order not found'), 0, Exception::HTTP_INTERNAL_ERROR);
      }

      if ($order->getStatus() == $order::STATE_COMPLETE) {
          throw new Exception(__('Order status is complete, can\'t change.'), 0, Exception::HTTP_BAD_REQUEST);
      }

      $payment = $order->getPayment();
      $payment->setAdditionalInformation('authorization_code', $auth_code);
      $payment->setAdditionalInformation('status_detail', $status_detail);
      $payment->setAdditionalInformation('message', $message);
      $payment->setAdditionalInformation('carrier_code', $carrier_code);

      $pg_status_m2 = [
          0 => $order::STATE_PENDING_PAYMENT,
          3 => $order::STATE_PROCESSING,
          7 => 'refund',
          8 => $order::STATE_CANCELED,
      ];
      $status_code = $pg_status_m2[$status_detail];
      if (in_array($status_detail, [2, 3, 4, 5, 30, 38, 39, 41, 42, 43]) || $status_code == $order::STATE_CANCELED) {
          $transaction_id_m2 = !is_null($payment->getParentTransactionId()) ? $payment->getParentTransactionId() : $payment->getTransactionId();
          $payment->setAmountCanceled($amount);
          $payment->setTransactionId($transaction_id_m2);
          $payment->setIsTransactionClosed(1);
          $payment->setShouldCloseParentTransaction(1);
      } else {
          $order->setStatus($status_code);
          $order->save();
      }
      $payment->save();
    }

    /**
    * Method to validate the request stoken authenticy.
    * @param string $user_id
    * @param string $transaction_id
    * @param string $application_code
    * @param string $pg_stoken
    * @param GatewayConfig $config
    * @return Exception
    */
    private function validateStoken($user_id, $transaction_id, $application_code, $pg_stoken, $config) {
        $credentials_client = $config->getServerCredentials();
        $credentials_server = $config->getClientCredentials();
        $codes_keys         = [
            $credentials_client['application_code'] => $credentials_client['application_key'],
            $credentials_server['application_code'] => $credentials_server['application_key'],
        ];
        $app_key = $codes_keys[$application_code];
        $for_md5 = "{$transaction_id}_{$application_code}_{$user_id}_{$app_key}";
        $stoken  = md5($for_md5);
        if ($stoken != $pg_stoken) {
            throw new Exception(__('stokens did not match.'), 0, Exception::HTTP_UNAUTHORIZED);
        }
    }
}
