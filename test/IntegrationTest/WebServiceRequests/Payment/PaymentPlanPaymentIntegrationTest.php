<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanPaymentIntegrationTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Use to get paymentPlanParams to be able to test PaymentPlanRequest
     * @return type
     */
    private function getGetPaymentPlanParamsForTesting() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $response = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();
         return $response->campaignCodes[0]->campaignCode;
    }
    
    public function testPaymentPlanRequestReturnsAcceptedResult() {
        $campaigncode = $this->getGetPaymentPlanParamsForTesting();
        $request = WebPay::createOrder()
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber(1)
                        ->setQuantity(2)
                        ->setAmountExVat(1000.00)
                        ->setDescription("Specification")
                        ->setName('Prod')
                        ->setUnit("st")
                        ->setVatPercent(25)
                        ->setDiscountPercent(0)
                )
                ->addCustomerDetails(WebPayItem::individualCustomer()
                        ->setNationalIdNumber(194605092222)
                        ->setInitials("SB")
                        ->setBirthDate(1923, 12, 12)
                        ->setName("Tess", "Testson")
                        ->setEmail("test@svea.com")
                        ->setPhoneNumber(999999)
                        ->setIpAddress("123.123.123")
                        ->setStreetAddress("Gatan", 23)
                        ->setCoAddress("c/o Eriksson")
                        ->setZipCode(9999)
                        ->setLocality("Stan")
                )
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePaymentPlanPayment($campaigncode)// returnerar InvoiceOrder object
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
    }
}
