<?php

class BBPushComponent extends Component {
	
	protected $tag = 'push';
    
	public function sendPush($message) {
		
		$bbconfig = Configure::read("settings.push.BlackBerry");
		
		//PAP URL
		$papURL = $bbconfig["url"];

		// APP ID provided by RIM
		$appid = $bbconfig["appid"];

		// Password provided by RIM
		$password = $bbconfig["password"];

		//Deliver before timestamp
		$deliverbefore = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 minutes'));

		//An array of address must be in PIN format or "push_all"
		$addresstosendto[0] = 'push_all';
		//$addresstosendto[0] = '29AAA7BF';

		$addresses = '';
		foreach ($addresstosendto as $value) {
			$addresses .= '<address address-value="' . $value . '"/>';
		}

		$boundary = "mPsbVQo0a68eIL3OAxnm";

		// create a new cURL resource
		$err = false;
		$ch = curl_init();
		$messageid = microtime(true);

		//$message = '1|Esto es una prueba|27/08/2012|direccion|extras|jazz|0212848747|22.8|78.87726|0.2000344';

		$data = '--'.$boundary. "\r\n" .
		'Content-Type: application/xml; charset=UTF-8' . "\r\n\r\n" .
		'<?xml version="1.0"?>
		<!DOCTYPE pap PUBLIC "-//WAPFORUM//DTD PAP 2.1//EN" "http://www.openmobilealliance.org/tech/DTD/pap_2.1.dtd">
		<pap>
		<push-message push-id="' . $messageid . '" deliver-before-timestamp="' . $deliverbefore . '" source-reference="' . $appid . '">'
		. $addresses .
		'<quality-of-service delivery-method="unconfirmed"/>
		</push-message>
		</pap>' . "\r\n" .
		'--'.$boundary. "\r\n" .
		'Content-Type: text/plain' . "\r\n" .
		'Push-Message-ID: ' . $messageid . "\r\n\r\n" .
		stripslashes($message) . "\r\n" .
		'--' . $boundary . '--' . "\n\r";

		// set URL and other appropriate options

		curl_setopt($ch, CURLOPT_URL, $papURL);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, "BlackBerry Push Service SDK/1.0");
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD,$appid.':'.$password );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/related; boundary=". $boundary ."; type=application/xml", "Accept: text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2", "Connection: keep-alive"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// grab URL and pass it to the browser
		$xmldata = curl_exec($ch);
		$result_info = curl_getinfo($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);

		//Start parsing response into XML data that we can read and output
		$p = xml_parser_create();
		xml_parse_into_struct($p, $xmldata, $vals);
		$errorcode = xml_get_error_code($p);
		if ($errorcode > 0) {
			$err = true;
		}
		xml_parser_free($p);

		$this->log('Message: ' . $message, $this->tag);
		$this->log('Our PUSH-ID: ' . $messageid, $this->tag);
		$this->log($vals, $this->tag);
		/*
		echo 'Our PUSH-ID: ' . $messageid . "<br \>\n";
		if (!$err && $vals[1]['tag'] == 'PUSH-RESPONSE') {
			echo 'PUSH-ID: ' . $vals[1]['attributes']['PUSH-ID'] . "<br \>\n";
			echo 'REPLY-TIME: ' . $vals[1]['attributes']['REPLY-TIME'] . "<br \>\n";
			echo 'Response CODE: ' . $vals[2]['attributes']['CODE'] . "<br \>\n";
			echo 'Response DESC: ' . $vals[2]['attributes']['DESC'] . "<br \> \n";
		} elseif ($err) {
			echo '<p>An XML parser error has occured</p>' . "\n";
			echo '<pre>' . xml_error_string($errorcode) ."</pre>\n";
			echo '<p>Response</p>' . "\n";
			echo '<pre>' . $xmldata . '</pre>' . "\n";
		} else {
			echo '<p>An error has occured</p>' . "\n";
			echo 'Error CODE: ' . $vals[1]['attributes']['CODE'] . "<br \>\n";
			echo 'Error DESC: ' . $vals[1]['attributes']['DESC'] . "<br \>\n";
		}
		*/
	}
	
}