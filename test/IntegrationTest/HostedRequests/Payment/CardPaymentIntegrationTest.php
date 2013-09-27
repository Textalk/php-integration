<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentIntegrationTest extends \PHPUnit_Framework_TestCase {
    
    public function testDoCardPaymentRequest() {
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->addDiscount(WebPayItem::relativeDiscount()
                        ->setDiscountId("1")
                        ->setDiscountPercent(50)
                        ->setUnit("st")
                        ->setName('Relative')
                        ->setDescription("RelativeDiscount")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber(rand(0, 1000))
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();
        
        $url = "https://test.sveaekonomi.se/webpay/payment";
        
        /** CURL  **/
        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        curl_exec($ch);
        
        // Check if any error occurred
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            $payPage = "";
            $response = $info['http_code'];
            
            if (isset($info['redirect_url'])) {
                $payPage = $info['redirect_url'];
            }
        }
        curl_close($ch);
        
        if ($response) {
            $status = $response;
            $redirect = substr($payPage, 41, 7);
        } else {
            $status = 'No answer';
        }
        
        $this->assertEquals(302, $status); //Curl response code "Found"
        $this->assertEquals("payPage", $redirect);
    }
}
