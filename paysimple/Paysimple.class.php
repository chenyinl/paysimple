<?php
include(__DIR__."/Paysimple.config.php");

/**
 * The config file should define the following:
 * PAYSIMPLE_USER
 * PAYSIMPLE_SECRET
 * PAYSIMPLE_URL: https://54.186.59.23, 
 *     https://sandbox-api.paysimple.com,
 *     or https://api.paysimple.com
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
class PaySimple
{
	/**
	 * Customer infomation to sent to API
	 */
	public $customerInfo;
	
	/**
	 * Customer ID received from API returns
	 */
	private $customerId;
    
    /**
     * Payment Object Info
     * Payment method, card No. etc from the form
     */
    public $paymentMethodInfo;
    
    /**
     * Payment Method/Object Id
     * Payment object id returned from API
     */
    private $paymentMethodId;
    
    /**
     * Payment Info
     * Payment Object ID and Amount
     */
    public $paymentInfo;
    
    /**
     * Payment success tracking number
     * Payment tracking number returned from API
     */
    private $trackNumber;
    
    /**
     * Create a header for Auth
     */
    private function setAuthHeader()
    {
        $timestamp = gmdate("c", time());
        // use raw output
		$hmac = hash_hmac("sha256", $timestamp, PAYSIMPLE_SECRET, true);
		$hmac = base64_encode($hmac);
        return "Authorization: PSSERVER accessId=".PAYSIMPLE_USER.
            ";timestamp=".$timestamp.";signature=".$hmac;
    }    

    /**
     * List all the customers
     * A testing API
     */
	public function listCustomers()
	{
		$headers = array();
		$headers[]= $this->setAuthHeader();
		$url = PAYSIMPLE_URL."/v4/customer";
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPGET, TRUE );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $curl, CURLOPT_VERBOSE, TRUE );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		$response = curl_exec( $curl );
        if( !$response){
            throw new Exception( "No response from list customer API");
        }
        echo $response;
		$json = json_decode( $response, true );
		$responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		curl_close($curl);
	}

	public function setCustomer( $customerInfo )
	{
		$this->customerInfo = array(
			"BillingAddress" => array(
				"StreetAddress1" => $customerInfo["StreetAddress1"],
				"StreetAddress2" => $customerInfo["StreetAddress2"],
				"City" => $customerInfo["City"],
				"StateCode" => $customerInfo["StateCode"],
				"ZipCode" => $customerInfo["ZipCode"],
				"Country" => $customerInfo["Country"]
			),
			"ShippingSameAsBilling" => $customerInfo["ShippingSameAsBilling"],
			"ShippingAddress" => $customerInfo["ShippingAddress"],
			"Company" => $customerInfo["Company"],
			"Notes" => $customerInfo["Notes"],
			"CustomerAccount" => $customerInfo["CustomerAccount"],
			"FirstName" => $customerInfo["FirstName"],
			"LastName" => $customerInfo["LastName"],
			"Email" => $customerInfo["Email"],
			"Phone" => $customerInfo["Phone"]
		);
	}
    
	/**
	 * send customer object to API
	 * @return customerId
	 */
	public function createCustomer()
	{
		$headers = array();
		$headers[]="Content-Type: application/json";
		$headers[]= $this->setAuthHeader();
		$url = PAYSIMPLE_URL."/v4/customer";

		$data_string = json_encode( $this->customerInfo );

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, TRUE); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		$response = curl_exec( $curl );
        if( !$response){
            throw new Exception( "No response from create customer");
        }
		$json = json_decode( $response, true );
		$this->customerId = $json["Response"]["Id"];
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
	}
    
    /**
     * Get Customer ID, was from the API
     */
    public function getCustomerId(){
        return $this->customerId;
    }
    
    /**
     * Set Payment method object information
     */
	public function setPaymentMethodInfo( $paymentInfo )
	{
		$this->paymentMethodInfo = array(
			"CreditCardNumber" => $paymentInfo["CreditCardNumber"],
            "ExpirationDate" => $paymentInfo["ExpirationDate"],
            "Issuer" => $paymentInfo["Issuer"],
            "BillingZipCode" => $paymentInfo["BillingZipCode"],
    		"CustomerId" => $paymentInfo["CustomerId"],
    		"IsDefault" => $paymentInfo["IsDefault"],
    		"Id" => $paymentInfo["Id"]
		);
	}
    
    /**
     * Call API and create payment object
     */
    public function createPaymentObj()
    {
        $headers = array();
        $headers[]="Content-Type: application/json";
        $headers[]= $this->setAuthHeader();
        $url = PAYSIMPLE_URL."/v4/account/creditcard";

        $data_string = json_encode( $this->paymentMethodInfo );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec( $curl );
        if( !$response){
            throw new Exception( "No response from creating payment method");
        }
        $json = json_decode( $response, true );
        
        $this->paymentMethodId = $json["Response"]["Id"];
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    }
    
    /**
     * return payment method/ object Id
     */
    public function getPaymentMethodId()
    {
        return $this->paymentMethodId;
    }
    
    /**
     * Set Payment information
     */
    public function setPaymentInfo( $paymentInfo)
    {
        $this->paymentInfo = array(
            "AccountId" => $paymentInfo["AccountId"],
            "Amount" => $paymentInfo["Amount"]
        );
    }
    
    /**
     * Post payment
     */
    public function postPayment()
    {
        $headers = array();
        $headers[]="Content-Type: application/json";
        $headers[]= $this->setAuthHeader();
        $url = PAYSIMPLE_URL."/v4/payment";

        $data_string = json_encode( $this->paymentInfo );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec( $curl );
        if( !$response){
            throw new Exception( "No response from posting payment");
        }
        $json = json_decode( $response, true );
        $this->trackNumber = $json["Response"]["TraceNumber"];
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    }
    
    /**
     * get the success paymenttrack number returned from API
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }
}
