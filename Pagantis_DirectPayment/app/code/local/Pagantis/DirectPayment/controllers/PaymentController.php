<?php
/**
 * Pagantis payment module for magento
 *
 * @package     Pagantis_DirectPayment
 * @copyright   Copyright (c) 2014  Pagantis (http://www.pagantis.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Pagantis direct payment controller 
 *
 * @package    Pagantis_DirectPayment
 * @author     Epsilon Eridani CB <contact@epsilon-eridani.com>
 */
class Pagantis_DirectPayment_PaymentController extends Mage_Core_Controller_Front_Action {
	
    
    
    /*
     *  The redirect action is triggered when someone places an order
     * 
     */ 
	public function redirectAction() {
       
        
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('pagantis_directpayment/standard_redirect');
        
        $block->setTemplate('pagantis_directpayment/redirect.phtml');
        
       
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();        
    
	}
	
    /*
     *  The response action is triggered from Pagantis gateway 
     * 
     */ 
    public function responseAction()
    {


        $json = file_get_contents('php://input');
        $notification = json_decode($json, true);   
        
        
        if(isset($notification['event']) && $notification['event'] == 'sale.created')  {
            
            // customer is in the pagantis gateway page, but the payment is not complete
            // se ha abierto la pagina de pago, pero todavia no se ha realizado el cobro
            return true;
        }


        if(isset($notification['event']) && $notification['event'] == 'charge.created')  {

            // payment ok
            // update order

            $order_id_from_pagantis = $notification['data']['order_id'];

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($order_id_from_pagantis);


            // check if order exists
            if($order->getId()) {
                
                $new_order_status = Mage::getModel('pagantis_directpayment/standard')->getConfigData('order_status');
                
                $order_state_msg  = 'Pagantis Auth: '.$notification['data']['authorization_code'];
                $order_state_msg .= ' ID: '.$notification['data']['id'];
                
                // update order status
                $order->setState($new_order_status, $new_order_status, Mage::helper('pagantis_directpayment')->__($order_state_msg), true);
                $order->save();
                
                $order->sendNewOrderEmail();
                
                // quote no longer active
                Mage::getModel('sales/quote')
                    ->load($order->getQuoteId())
                    ->setIsActive(false)
                    ->save();      
                    
                              
            }
            
        } else {
            
            // payment ko
            // pagantis rejected the payment
            
            $order_id_from_pagantis = $notification['data']['order_id'];

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($order_id_from_pagantis);
            
            $order_state_msg  = 'Error: '.$notification['data']['error_code'];
            $order_state_msg .= ' :: '.$notification['data']['error_message'];                    
            
            // to get back products stocks:
            // Admin>Catalog>Inventory>Stock Options/Set Items’ Status to be In Stock When Order is Cancelled = Yes 
            $order->cancel();
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
            $order->save();
            
            
            // get back items to cart
            Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(true)
                ->save();

        }

    }
    
    
    /*
     *  The cancel action is triggered when custormer returns to magento (payment ko)
     * 
     */ 
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');

        $session->addError(Mage::helper('pagantis_directpayment')->__('Something was wrong with your payment at Pagantis.com'));
        $this->_redirect('checkout/cart');
    }


    /*
     *  The success action is triggered when custormer returns to magento (payment ok)
     * 
     */ 
    public function successAction()
    {

        $session = Mage::getSingleton('checkout/session');
        
        $session->addsuccess(Mage::helper('pagantis_directpayment')->__('Congratulations! Your payment has been successfully processed.'));
        $this->_redirect('checkout/onepage/success');
    }    
    
   
    
}
