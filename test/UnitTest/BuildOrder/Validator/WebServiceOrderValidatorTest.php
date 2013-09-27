<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceOrderValidatorTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : Customer values are required for Invoice and PaymentPlan orders.

    function te_stFailOnMissingCustomerIdentity() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->beginOrderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                ->endOrderRow()
                ->setCountryCode("SE")
                    ->useInvoicePayment();
        $order->prepareRequest();

    }
      *
      */

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -duplicated value : Customer is either an individual or a company. You can not use function setNationalIdNumber() in combination with setNationalIdNumber() or setVatNumber().
     */
    public function t_estFailOnDoubleIdentity() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->addCustomerDetails(\WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
                ->useInvoicePayment();
        
        $order->prepareRequest();
    }
    
    /**
     * Use to get paymentPlanParams to be able to test PaymentPlanRequest
     * @return type
     */
    public function getGetPaymentPlanParamsForTesting() {
        $addressRequest = \WebPay::getPaymentPlanParams();
        $response = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();
        return $response->campaignCodes[0]->campaignCode;
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -Wrong customer type : PaymentPlanPayment not allowed for Company customer.
     */
    public function testFailOnCompanyPaymentPlanPayment() {
        $code = $this->getGetPaymentPlanParamsForTesting();
        $builder = \WebPay::CreateOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
                ->usePaymentPlanPayment($code);
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -not valid : Given countrycode does not exist in our system.
     */
    public function testFailOnBadCountryCode() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                 ->setCountryCode("ZZ")
                 ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                 ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(111111))
                 ->useInvoicePayment();
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    public function testFailOnMissingCountryCode() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(111111))
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->useInvoicePayment();
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().
     */
    public function testFailOnMissingNationalIdNumberForSeOrder() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setName("Tess", "Testson"))
                ->useInvoicePayment();
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : OrgNumber is required for company customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().
     */
    public function testFailOnMissingOrgNumberForCompanyOrderSe() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                  ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                  ->addCustomerDetails(\WebPayItem::companyCustomer()->setCompanyName("Mycompany"))
                    ->useInvoicePayment();
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
     * -missing value : Name is required for individual customers when countrycode is DE. Use function setName().
     * -missing value : StreetAddress is required for all customers when countrycode is DE. Use function setStreetAddress().
     * -missing value : Locality is required for all customers when countrycode is DE. Use function setLocality().
     * -missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().
     */
    public function testFailOnMissingIdentityValuesForDEPaymentPlanOrder() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("DE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->usePaymentPlanPayment(213060);
        
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
     */
    public function testFailOnMissingBirthDateForDeOrder() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("DE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()
                        //->setBirthDate(1923, 12, 12)
                        ->setName("Tess", "Testson")
                        ->setStreetAddress("Gatan", 23)
                        ->setZipCode(9999)
                        ->setLocality("Stan")
                )
                ->useInvoicePayment();
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
     * -missing value : BirthDate is required for individual customers when countrycode is NL. Use function setBirthDate().
     * -missing value : Name is required for individual customers when countrycode is NL. Use function setName().
     * -missing value : StreetAddress is required for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode().
     */
    public function testFailOnMissingValuesForNlOrder() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("NL")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->useInvoicePayment();
        //$errorArray = $order->validateOrder();
        //print_r($errorArray);
        $order->prepareRequest(); //throws esception
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
     */
    public function testFailOnMissingInitialsForNlOrder() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("NL")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()
                        //->setInitials("SB")
                        ->setBirthDate(1923, 12, 12)
                        ->setName("Tess", "Testson")
                        ->setStreetAddress("Gatan", 23)
                        ->setZipCode(9999)
                        ->setLocality("Stan")
                )
                ->useInvoicePayment();
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing values : OrderRows are required. Use function addOrderRow(WebPayItem::orderRow) to get orderrow setters.
     */
    public function testFailOnMissingOrderRows() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->setCountryCode("SE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
                ->useInvoicePayment();
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing values : At least two of the values must be set in object \WebPayItem::  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     * -missing value : Quantity is required in object \WebPayItem. Use function \WebPayItem::setQuantity().
     */
    public function testFailOnMissingOrderRowValues() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\WebPayItem::orderRow())
                ->setCountryCode("SE")
                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
                ->useInvoicePayment();
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing values : OrderDate is Required. Use function setOrderDate().
    */
    public function testFailOnMissingOrderDate() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                // ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
                ->useInvoicePayment();
        $order->prepareRequest();
    }
    
    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -incorrect datatype : Vat must be set as Integer.
    */
    public function testFailOnVatAsFloat() {
        $builder = \WebPay::createOrder();
        $order = $builder
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setAmountExVat(100)
                    ->setVatPercent(20.33)
                    ->setQuantity(1)
                )
                ->setCountryCode("SE")
                // ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
                ->useInvoicePayment();
        $order->prepareRequest(); 
    }
}
