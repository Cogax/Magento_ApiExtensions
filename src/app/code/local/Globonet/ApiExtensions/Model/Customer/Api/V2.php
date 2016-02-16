<?php

/**
 * Extension of Magento Soap API V1
 */
class Globonet_ApiExtensions_Model_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api_V2
{

  public function login($email, $password) {
    return Mage::getSingleton('Globonet_ApiExtensions_Model_Customer_Api')->login(
      $email,
      $password
    );
  }

  public function logout() {
    return Mage::getSingleton('Globonet_ApiExtensions_Model_Customer_Api')->logout();
  }

}
