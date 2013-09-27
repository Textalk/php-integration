<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Returns Array with all paymentmethods
 * conected to the merchantId and/or ClientId
 *
 * @author anne-hal
 */
class GetPaymentMethods {

    private $method = "getpaymentmethods";
    private $config;
    private $countryCode = "SE";//Default SE

    function __construct($config) {
        $this->config = $config;
    }

    public function setContryCode($countryCodeAsString){
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    public function prepareRequest(){

        $xmlBuilder = new HostedXmlBuilder();
        $requestXML = $xmlBuilder->getPaymentMethodsXML($this->config->getMerchantId("HOSTED",  $this->countryCode));
        $request = array(   'merchantid' => urlencode($this->config->getMerchantId("HOSTED",  $this->countryCode)),
                            'message' => urlencode(base64_encode($requestXML)),
                            'mac' => urlencode(hash("sha512", base64_encode($requestXML) . $this->config->getSecret("HOSTED",  $this->countryCode)))
                        );
        return $request;
    }
    /**
     * Do request using cURL
     * @return array
     */
    public function doRequest(){
        $fields = $this->prepareRequest();
               $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint(SveaConfigurationProvider::HOSTED_ADMIN_TYPE).  $this->method);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        $responseXML = curl_exec($ch);

        $responseObj = new \SimpleXMLElement($responseXML);


        $sveaResponse = new \SveaResponse($responseObj, $this->countryCode, $this->config);
        $paymentmethods = array();
        foreach ($sveaResponse->response->paymentMethods as $method) {
            $paymentmethods[] = (string)$method;
        }
        //Add Invoice and Paymentplan. If there is a clientnumber for invoice, we assume you hav it configured at Svea
        $clientIdInvoice = $this->config->getClientNumber(\PaymentMethod::INVOICE,  $this->countryCode);
        if(is_numeric($clientIdInvoice) && strlen($clientIdInvoice) > 0 ){
            $paymentmethods[] = \PaymentMethod::INVOICE;
        }
        $clientIdPaymentPlan = $this->config->getClientNumber(\PaymentMethod::PAYMENTPLAN, $this->countryCode);
        if(is_numeric($clientIdPaymentPlan) && strlen($clientIdPaymentPlan) > 0 ){
            $paymentmethods[] = \PaymentMethod::PAYMENTPLAN;
        }
        curl_close($ch);

       return $paymentmethods;

    }
}