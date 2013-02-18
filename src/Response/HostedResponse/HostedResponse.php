<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';
/**
 * Description of HostedResponse
 *
 * @author anne-hal
 */
class HostedResponse {
   
    public $accepted;
    public $resultcode;
    public $transactionId;
    public $clientOrderNumber;
    public $paymentMethod;
    public $merchantId;
    public $amount;
    public $currency;  


    function __construct($response,$secret) {
        if(is_array($response)){
            if(array_key_exists("response",$response) && array_key_exists("mac",$response)){
                $decodedXml = base64_decode($response['response']);
                if($this->validateMac($response['response'],$response['mac'],$secret)){           
                    $this->formatXml($decodedXml);
                }  else {
                    $this->accepted = 0;
                    $this->resultcode = "Response failed authorization. MAC not valid.";
                }
            }             
        }else{
            $this->accepted = 0;
            $this->resultcode = "Response is not recognized.";
        }
        
       
    }
    
    protected function formatXml($xml){
     $xmlElement = new SimpleXMLElement($xml);
     if((string)$xmlElement->statuscode == 0){
          $this->accepted = 1;
     }else{
         $this->accepted = 0;
         $this->resultcode = (int)$xmlElement->statuscode;
     }
     $this->transactionId = (string)$xmlElement->transaction['id'];
     $this->paymentMethod = (string)$xmlElement->transaction->paymentmethod;
     $this->merchantId = (string)$xmlElement->transaction->merchantid;     
     $this->clientOrderNumber = (string)$xmlElement->transaction->customerrefno;
     $minorAmount = (int)($xmlElement->transaction->amount);
     $this->amount = $minorAmount * 0.01;
     $this->currency = (string)$xmlElement->transaction->currency;
     if(property_exists($xmlElement->transaction, "subscriptionid")){
         $this->subscriptionId = (string)$xmlElement->transaction->subscriptionid;    
         $this->subscriptionType = (string)$xmlElement->transaction->subscriptiontype;    
     }
     if(property_exists($xmlElement->transaction, "cardtype")){
        $this->cardType = (string)$xmlElement->transaction->cardtype;    
        $this->maskedCardNumber = (string)$xmlElement->transaction->maskedcardno;    
        $this->expiryMonth = (string)$xmlElement->transaction->expirymonth;    
        $this->expiryYear = (string)$xmlElement->transaction->expiryyear;    
        $this->authCode = (string)$xmlElement->transaction->authcode;    
     }
     
       
    }
    
    public function validateMac($messageEncoded,$mac,$secret){
        if($secret == null){
            $secret = SveaConfig::getConfig()->secret;
        }
        $macKey = hash("sha512", $messageEncoded.$secret);
        if($mac == $macKey){
            return TRUE;
        }
        return FALSE;
    }
}

?>
