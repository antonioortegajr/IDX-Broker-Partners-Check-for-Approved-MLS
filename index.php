<?php

 //Let's set up some variables for easy editing

 //Your Partner API key
 $partner_api_key = 'Your Partner API key';

 //email address to send notification
 $email = 'Your Email';

 // Partner level API call for https://api.idxbroker.com/partners/clients here
 $url = 'https://api.idxbroker.com/partners/clients';
 $method = 'GET';

 // headers (required and optional)
 $headers = array(
	'Content-Type: application/x-www-form-urlencoded', // required
	'accesskey: '.$partner_api_key, // required - replace with your own
	'outputtype: json' // optional - overrides the preferences in our API control page
 );

 // set up cURL
 $handle = curl_init();
 curl_setopt($handle, CURLOPT_URL, $url);
 curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
 curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
 curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

 // exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
 $response = curl_exec($handle);
 $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

 if ($code >= 200 || $code < 300)
	 $clients = json_decode($response,true);
 else
	 $error = $code;


 //loop though to get all AIDs, API Keys, and Account Status. Add to an array.
 $array_from_return = array();

 foreach ($clients as $key => $value) {
   if($value["accountStatus"] === "enabled"){
      $add = array("aid" => $value["accountID"], "api_key"=> $value["apikey"],"status"=> $value["accountStatus"]);
      array_push($array_from_return, $add);
 }
 }
 //Now we have only enabled accounts

 //new array to store apprved accounts with no approved MLSs
 $enabled_no_mls = array();

 //loop throught the enabaled accounts and check for approved MLSs via another API call. https://api.idxbroker.com/mls/approvedmls
 foreach ($array_from_return as $key => $value) {

  // access URL and request method
   $url = 'https://api.idxbroker.com/mls/approvedmls';
   $method = 'GET';

  // headers (required and optional)
   $headers = array(
  	 'Content-Type: application/x-www-form-urlencoded', // required
  	 'accesskey: '.$value["api_key"], // required - replace with your own
  	 'ancillarykey: '.$partner_api_key, // optional and for partners only - replace with your own
  	 'outputtype: json' // optional - overrides the preferences in our API control page
  );

  // set up cURL
   $handle = curl_init();
   curl_setopt($handle, CURLOPT_URL, $url);
   curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

  // exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
   $response = curl_exec($handle);
   $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

 // check for 204 meaning no approved MLS
 if($code == 204){
//keep moving along
 }
 else{
 //store the AIDs with no approved MLS in an array
   array_push($enabled_no_mls, $value["aid"]);
 }

 }

//prepare message with AIDs
 foreach ($enabled_no_mls as $key => $value) {
  $finds = $finds.$value.', ';
 }

 //email report to address in variable set waay back at the begining
  $email_report = 'The following AIDs are enabled, but have NO approved MLS:'.$finds;
  date_default_timezone_set('UTC');
  $test_date = date("F j, Y, g:i a");
  $to =  $email;

  $subject = 'IDX AIDs enabled w/no approved MLS. AID: ' . $test_date;
  $message = $email_report.' END of Report. These accounts should be checked to see where they are in the MLS approval process.';
  $headers =  $email. "\r\n" .
      'Reply-To: '.$email . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
      $headers .= 'Cc: ' . $email_cc . "\r\n";
 mail($to, $subject, $message, $headers);
  //echo something incase you run manully.
 echo 'if run manually, this script has run';
?>
