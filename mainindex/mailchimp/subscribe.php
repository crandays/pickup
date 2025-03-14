<?php

/**
 *	PHP Mailchimp Newsletter Integration
 *	Package: Prime Cab
 *  File For Mailchimp Newsletter Subscription
 */


// Add your mailchimp API key which you can get from your mailchimp account under Account > Extras > API keys
// Example: 91d65090be59392d88951e9b160f3111-xyz
define('API_KEY', '');

// Add mailchimp list id to which you want user to subscribe
// Example: 63c3d4f789
define('LIST_ID', '');	



function sendSubscribeRequest(){

	//Mailchimp API URL
	$url = 'https://'.substr(API_KEY,strpos(API_KEY,'-')+1).'.api.mailchimp.com/3.0/lists/' .LIST_ID. '/members/';

	$subscription_data = array(
    	'email_address' => $_POST['email_address'],
  		'status'        => 'subscribed',
  	);
	$encode_sub_data = json_encode($subscription_data);

	//setup CURL request
	$ch = curl_init();
	//set parameters for CURL
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERPWD, 'api_key:'.API_KEY);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 180);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $encode_sub_data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$result_info = curl_exec($ch); // store response
	$mailchimp_resp=json_decode($result_info,true); //decode JSON response
  	$response = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get HTTP CODE
  	$errors = curl_error($ch); // store errors

 	curl_close($ch);

 	//Send API response back to Ajax request
 	$results = array(
   		'results' => $mailchimp_resp['title'],
   		'response' => $response,
   		'errors' => $errors
 	);

 	echo json_encode($results);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendSubscribeRequest();
    die();
}
