<?php

/**
 * Pagantis payment module for magento
 *
 * @package     Pagantis_DirectPayment
 * @copyright   Copyright (c) 2014  Pagantis (http://www.pagantis.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Pagantis direct payment module for magento 
 *
 * @package    Pagantis_DirectPayment
 * @author     Epsilon Eridani CB <contact@epsilon-eridani.com>
 */
class Pagantis_DirectPayment_Model_Standard extends Mage_Payment_Model_Method_Abstract {
    
    
	protected $_code = 'pagantis_directpayment';
	
    
    /**
     * Availability options
     */
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;

    /**
     * Currencies allowed in Pagantis 
     */    
    protected $_allowCurrencyCode = array( 'EUR', 'USD', 'GBP' );
    
    
    /**
     * Called when the order is placed
     *
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }
        
	
    /**
     * Return the URL to redirect to
     *
     * @return string
     */    
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('pagantis_directpayment/payment/redirect', array('_secure' => true));
	}
    
    

    /**
     * Return the checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return the current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote() {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }
    

    /**
     * Validate order and allowed currencies
     * 
     * @return Pagantis_DirectPayment_Model_Standard
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if(!in_array($currency_code, $this->_allowCurrencyCode))
        {
            Mage::throwException(Mage::helper('pagantis_directpayment')->__('Actual currency (%s) is not accepted by Pagantis payment gateway', $currency_code));
        }
        return $this;
    }
    
    
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }


    /**
     * Make the Pagantis gateway request
     * 
     * @return array 
     */
    public function getStandardCheckoutFormFields()
    {
        $a = $this->getQuote()->getShippingAddress();

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());

        $convertor = Mage::getModel('sales/convert_order');
        $invoice = $convertor->toInvoice($order);

        $amount = $order->getTotalDue() * 100;
        $current_order_id = $this->getCheckout()->getLastRealOrderId();

        $pagantis_account_id = trim( $this->getConfigData('accountid') );
        $currency = $order->getOrderCurrencyCode();
        $pagantis_secret = trim( $this->getConfigData('cryptokey') );
        
        
        $pagantis_ok_url = trim(Mage::getUrl('pagantis_directpayment/payment/success', array('_nosid' => true ) ) );
        $pagantis_nok_url = trim(Mage::getUrl('pagantis_directpayment/payment/cancel', array('_nosid' => true ) ) );
        
        $message = $pagantis_secret.$pagantis_account_id.$current_order_id.$amount.$currency.'SHA1'.$pagantis_ok_url.$pagantis_nok_url;
        
        $signature = sha1($message);

        $arrayHiddenFields = array (
        
            'order_id' => $current_order_id,
            'auth_method' => 'SHA1',
            'amount' => $amount,
            'currency' => $currency,
            'description' => $this->getConfigData('paymentcomment'). ' '.$current_order_id,
            'ok_url' => $pagantis_ok_url,
            'nok_url' => $pagantis_nok_url,
            'account_id' => $pagantis_account_id,
            'signature' => $signature
        );
        
        
        return $arrayHiddenFields;
    }


    /**
     * Return Pagantis gateway URL as defined in config.xml
     * 
     * @return string
     */
    public function getPagantisUrl()
    {
        return $this->getConfigData('api_url');
    }
    
}
?>
