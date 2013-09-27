<?php
namespace Svea;

/**
 * @author Jonas Lith
 */
class TestConf implements \ConfigurationProvider {
    
    public function getEndPoint($type) {
        return "url";
    }
    
    public function getMerchantId($type, $country) {
        return "merchant";
    }
    
    public function getPassword($type, $country) {
        return "pass";
    }
    
    public function getSecret($type, $country) {
        return "secret";
    }
    
    public function getUsername($type, $country) {
        return "username";
    }
    
    public function getClientNumber($type, $country) {
        return "clientnumber";
    }
}
