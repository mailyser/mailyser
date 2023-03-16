<?php
$uniqueId = $_REQUEST['id'];


$url = "https://www.mail-tester.com/".$uniqueId."&format=json";

//  Initiate curl
$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$mailTestJson = (json_decode($result, true));

echo $mailTestJson['body']['html']['content'];
?>