<?php
/**
 * Pagantis payment module for magento
 *
 * @package     Pagantis_DirectPayment
 * @copyright   Copyright (c) 2014  Pagantis (http://www.pagantis.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Pagantis direct payment redirect block 
 *
 * @package    Pagantis_DirectPayment
 * @author     Epsilon Eridani CB <contact@epsilon-eridani.com>
 */
class Pagantis_DirectPayment_Block_Standard_Redirect extends Mage_Core_Block_Template
{


    /**
     * Make the form with hidden fields to send the request to Pagantis gateway
     *
     */  
    protected function _construct()
    {
        parent::_construct();
        
        
        $standard = Mage::getModel('pagantis_directpayment/standard');
        
        $form = new Varien_Data_Form();
        $form->setAction($standard->getPagantisUrl())
            ->setId('pagantis_directpayment')
            ->setName('Pagantis')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach($standard->getStandardCheckoutFormFields() as $field => $value)
        {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        
        
        $form->addField('goPagantis', 'submit', array('name' => 'goPagantis', 'value' => Mage::helper('pagantis_directpayment')->__('Go to Pagantis'), 'style'=>'display:block; border: 1px #999 solid; background-color: #009FE3; padding: 5px 15px; color: white;'));
        //echo $form->toHtml();
        
        // save the form content using magic set
        // the content will be recovered in the template (getFormRedirect)
        $this->setFormRedirect($form->toHtml());
    }
}
