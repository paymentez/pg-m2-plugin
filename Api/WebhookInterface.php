<?php
/**
* Copyright © Magento, Inc. All rights reserved.
*/
namespace Paymentez\PaymentGateway\Api;
/**
* @api
*/
interface WebhookInterface
{
  /**
   * Update order via payment gateway webhook
   */
  public function updateOrderWebhook();
}
