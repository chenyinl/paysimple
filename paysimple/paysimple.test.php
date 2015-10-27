<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
include( "Paysimple.class.php");

if( $_GET["action"] == "listCustomers"){
	$o = new PaySimple();
	$o -> listCustomers();
	return;
}


if ($_GET["action"] == "onecall"){
	$cus = array(
		"StreetAddress1" => $_GET["StreetAddress1"],
		"StreetAddress2" => $_GET["StreetAddress2"],
		"City" => $_GET["City"],
		"StateCode" => $_GET["StateCode"],
		"ZipCode" => $_GET["ZipCode"],
		"Country" => $_GET["Country"],
		"CustomerAccount" => $_GET["CustomerAccount"],
		"FirstName" => $_GET["FirstName"],
		"LastName" => $_GET["LastName"],
		"Email" => $_GET["Email"],
		"Phone" => $_GET["Phone"],
		"ShippingSameAsBilling" => TRUE,
		"ShippingAddress" => "",
		"Company" => "",
		"Notes" => ""
	);
	$o = new PaySimple();
	$o -> setCustomer( $cus );
	$o -> createCustomer();
	$customerAccount = $o->getCustomerId();
    echo "<br/>Customer ID: ".$customerAccount;

	$paymentMethodInfo = array(
		"CreditCardNumber" => $_GET["CreditCardNumber"],
		"ExpirationDate" => $_GET["ExpirationDate"],
		"Issuer" => $_GET["Issuer"],
		"BillingZipCode" => $_GET["BillingZipCode"],
		"CustomerId" => $customerAccount,
		"IsDefault" => TRUE,
		"Id" => 0

	);
	$o -> setPaymentMethodInfo( $paymentMethodInfo );
	$o -> createPaymentObj();
    $paymentMethodId = $o->getPaymentMethodId();
    echo "<br/>Payment Method ID: ".$paymentMethodId;

	$paymentInfo = array(
		"AccountId" => $paymentMethodId,
		"Amount" => $_GET["Amount"]
	);
	$o -> setPaymentInfo( $paymentInfo );
	$o -> postPayment();
    echo "<br/>Tracking Number: ".$o->getTrackNumber();
	

}


if ($_GET["action"] == "postPayment"){
	$paymentInfo = array(
		"AccountId" => $_GET["AccountId"],
		"Amount" => $_GET["Amount"]
	);
	$o = new PaySimple();
	$o -> setPaymentInfo( $paymentInfo );
	$o -> postPayment();
	return;
}

if ($_GET["action"] == "createPaymentAccountObject"){
	$paymentInfo = array(
		"CreditCardNumber" => $_GET["CreditCardNumber"],
		"ExpirationDate" => $_GET["ExpirationDate"],
		"Issuer" => $_GET["Issuer"],
		"BillingZipCode" => $_GET["BillingZipCode"],
		"CustomerId" => $_GET["CustomerId"],
		"IsDefault" => $_GET["IsDefault"],
		"Id" => $_GET["Id"]

	);
	$o = new PaySimple();
	$o -> setPaymentMethodInfo( $paymentInfo );
	$o -> createPaymentObj();
	return;
}

if( $_GET["action"] == "createCustomerObject"){
	$cus = array(
		"StreetAddress1" => $_GET["StreetAddress1"],
		"StreetAddress2" => $_GET["StreetAddress2"],
		"City" => $_GET["City"],
		"StateCode" => $_GET["StateCode"],
		"ZipCode" => $_GET["ZipCode"],
		"Country" => $_GET["Country"],
		"ShippingSameAsBilling" => $_GET["ShippingSameAsBilling"],
		"ShippingAddress" => $_GET["ShippingAddress"],
		"Company" => $_GET["Company"],
		"Notes" => $_GET["Notes"],
		"CustomerAccount" => $_GET["CustomerAccount"],
		"FirstName" => $_GET["FirstName"],
		"LastName" => $_GET["LastName"],
		"Email" => $_GET["Email"],
		"Phone" => $_GET["Phone"]
	);
	$o = new PaySimple();
	$o -> setCustomer( $cus );
	$o -> createCustomer();
	return;
}
