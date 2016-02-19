<?php

/**
 * Extension of Magento Soap API V1
 */
class Globonet_ApiExtensions_Model_Customer_Api extends Mage_Customer_Model_Customer_Api
{
  /** @var Mage_Customer_Model_Session */
  protected $_customerSession = null;

  /**
   * Customer authentication.
   *
   * @param   string  $email Username of customer to authenticate
   * @param   string  $password Password of customer to authenticate
   * @return  string|null Session ID
   */
  public function login($email, $password, $store) {
    Mage::app()->setCurrentStore($store);

    // get customer session object
    $session = $this->_getCustomerSession();

    // authenticate customer
    try {
      $session->login($email, $password); // will throw an exception if login is invalid
      $customer_info = $this->info($this->_getAuthenticatedCustomerId());
      $customer_info['session'] = array(
        'session_id' => $session->getEncryptedSessionId(),
        'query_param' => $session->getSessionIdQueryParam(),
        'cookie_lifetime' => $session->getCookieLifetime(),
        'full_url' => Mage::getUrl( '/', array('_store' => $store, '_query' => array($session->getSessionIdQueryParam() => $session->getEncryptedSessionId() ), '_store_to_url' => true))
      );
      return $customer_info;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  /**
   * Logout authenticated customer, if any.
   * @return boolean True.
   */
  public function logout()
  {
    // get customer session object
    $session = $this->_getCustomerSession();

    // logout customer
    $session->logout();

    return true;
  }

  /**
   * Check whether a customer has been authenticated in this session.
   *
   * @return void
   * @throws Mage_Core_Exception If customer is not authenticated.
   */
  protected function _checkCustomerAuthentication()
  {
    // get customer session object
    $session = $this->_getCustomerSession();

    // check whether customer is logged in
    if ( !$session->isLoggedIn() ) {
      // if customer is not logged in throw an exception
      Mage::throwException(Mage::helper('mymodule')->__('Not logged in'));
    }
  }

  /**
   * Get authenticated customer object.
   *
   * @return Mage_Customer_Model_Customer Authenticated customer object.
   * @throws Mage_Core_Exception If customer is not authenticated or does not exist.
   */
  protected function _getAuthenticatedCustomer()
  {
    // retrieve authenticated customer ID
    $customerId = $this->_getAuthenticatedCustomerId();
    if ( $customerId )
    {
      // load customer
      /** @var Mage_Customer_Model_Customer $customer */
      $customer = Mage::getModel('customer/customer')
        ->load($customerId);
      if ( $customer->getId() ) {
        // if customer exists, return customer object
        return $customer;
      }
    }

    // customer not authenticated or does not exist, so throw exception
    Mage::throwException(Mage::helper('mymodule')->__('Unknown Customer'));
  }

  /**
   * Get authenticated customer ID.
   *
   * @return integer Authenticated customer ID, if any; null, otherwise.
   */
  protected function _getAuthenticatedCustomerId()
  {
    // get customer session object
    $session = $this->_getCustomerSession();

    // return authenticated customer ID, if any
    return $session->getCustomerId();
  }

  /**
   * @return Mage_Customer_Model_Session
   */
  protected function _getCustomerSession()
  {
    if ( !$this->_customerSession ) {
      $this->_customerSession = Mage::getSingleton('customer/session');
    }
    return $this->_customerSession;
  }
}