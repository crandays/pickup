<?php
/**
 *	PHP Form Email Script
 *	Package: Prime Cab
 *  File For Home 3 Page Contact Form For Sending Email
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
function sendEmail(){
	
	/* Declare Variables */
	$name='';
	$emailAdd='';
	$status='';
	$message='';
	$headers = '';
	
	if(!empty($_POST['formData'])){
		parse_str($_POST['formData'], $formData);
		$name= filter_var($formData['username'], FILTER_SANITIZE_STRING);
		$emailAdd= filter_var($formData['user_email'], FILTER_SANITIZE_EMAIL);
		$message= filter_var($formData['message'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
		
		if(!empty($name) && !empty($emailAdd) && !empty($message)){
			
			// Set Email Headers
			if (FROM_EMAIL !== '') {
				$headers .= 'From: '.FROM_EMAIL."\r\n";
			}
			$headers .= 'Reply-To: '.$emailAdd."\r\n";
			$headers .= 'Content-Type: text/plain; charset=UTF-8'."\r\n";

			/* Formatting Email Message */
			$title = 'New Message from '.$name;
			$message = 
				'You have received new message from your website. Check details below:'."\n\n"
				.'Sender IP Address: '.getIp()."\n"
				.'Name: '.$name."\n"
				.'Email: '.$emailAdd."\n"
				.'Message:'."\n"
				.$message;

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
function getIp() {
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
    sendEmail();
    die();
}