<?php
/**
 *	PHP Form Email Script
 *	Package: Prime Cab
 *  File For Ride Booking Detail To Customer And Site Admin
 */

// Note: Hosting servers can block sending emails with custom From field because of spam.
// Leave it empty if it not works with your hosting server
define('FROM_EMAIL', '');

// Add Recipient email address to which email message will be sent.
// Example: sarah@example.com
define('TO_EMAIL', '');	
	

/**
 * Function for sending email message
 * @return string ( Return Email Sending Status )
 */
function sendBookingEmail(){
	
	/* Declare Variables */
	$rider_name='';
	$rider_phNo='';
	$rider_email='';
	$status='';
	$headers = '';
	
	if(!empty($_POST['formData'])){
		parse_str($_POST['formData'], $formData);
		$rider_name = filter_var($formData['username'], FILTER_SANITIZE_STRING);
		$rider_phNo = filter_var($formData['phone_num'], FILTER_SANITIZE_EMAIL);
		$rider_email = filter_var($formData['email_id'], FILTER_SANITIZE_STRING);

		//Fetch Rider Booking Details
		$book_ref = $_POST['book_ref'];
		$start_loc = $_POST['start_loc'];
		$end_loc = $_POST['end_loc'];
		$pickup_date = $_POST['pickup_date'];
		$pickup_time = $_POST['pickup_time'];
		$dropoff_date = $_POST['dropoff_date'];
		$dropoff_time = $_POST['dropoff_time'];
		$service_type = $_POST['service_type'];
		$trip_time = $_POST['trip_time'];
		
		if(!empty($rider_name) && !empty($rider_phNo) && !empty($rider_email)){
			
			// Set Email Headers
			if (FROM_EMAIL !== '') {
				$headers .= 'From: '.FROM_EMAIL."\r\n";
			}
			$headers .= 'Reply-To: '.$rider_email."\r\n";
			$headers .= "CC: ".$rider_email.""."\r\n";
			$headers .= 'Content-Type: text/plain; charset=UTF-8'."\r\n";

			/* Formatting Email Message */
			$title = 'New Ride Booking Confirmation from '.$rider_name;
			$message = 
				'You have received a new ride booking from your website. Check details below:'."\n\n"
				.'Sender IP Address: '.getUserIp()."\n"
				.'Rider Name: '.$rider_name."\n"
				.'Rider Email: '.$rider_email."\n"
				.'Rider Phone Number: '.$rider_phNo."\n"
				.'******Booking Detail******'."\n"
				."Booking Reference ID: ".$book_ref."\n"
				."Service Type: ".$service_type."\n"
				."Pickup Location: ".$start_loc."\n"
				."Pickup Date: ".$pickup_date."\n"
				."Pickup Time: ".$pickup_time."\n"
				."Dropoff Location: ".$end_loc."\n"
				."Dropoff Date: ".$dropoff_date."\n"
				."Dropoff Time: ".$dropoff_time."\n"
				."Trip Estimation: ".$trip_time."\n";

			// Send Mail
			$result = mail(TO_EMAIL, $title, $message, $headers);
			if($result){
				$status=1;
				echo $status;
				die();
			}else{
				$status=0;
				echo $status;
				die();
			}
		}
	}
}	

/**
 * Function for getting user IP address
 * @return string ( Return IP Address )
 */
function getUserIp() {
    $ip = '';

    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } else if(getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } else if(getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    } else if(getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    } else if(getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    } else if(getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = 'N/A';
    }

    return $ip;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendBookingEmail();
    die();
}