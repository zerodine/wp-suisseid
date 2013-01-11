<?php 

require_once 'adminstuff.php';

class WPSUISSEID_Request {
	
	public function authnRequest() {
		$admin = new WPSUISSEID_Admin(true);
		
		$doc = new DOMDocument();
		$authnRequest = $doc->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol', 'saml2p:AuthnRequest');
		$authnRequest->setAttribute('Destination', $admin->get_option('url'));
		$authnRequest->setAttribute("Provider-Name", "SuisseID Service Provider AG");
		$authnRequest->setAttribute("ForceAuthn", "true");
		$authnRequest->setAttribute("ID", $this->gen_uuid());
		$authnRequest->setAttribute("Version", "2.0");
		$authnRequest->setAttribute("IssueInstant", "2012-12-18T09:00:00");
		$authnRequest->setAttribute("AssertionConsumerServiceURL", get_bloginfo('url').'/wp-login.php');#get_admin_url()); #);
		
		$issuer = $doc->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion', 'saml2:Issuer');
		$issuer->appendChild($doc->createTextNode($admin->get_option('spname')));
		$authnRequest->appendChild($issuer);
		
		$extensions = $doc->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol', 'saml2p:Extensions');
		$attributes = new WPSUISSEID_Attributes();
		
		foreach ( array_keys($attributes->getAttributes()) as $attribute) {
			if ( $admin->get_option("attribute_".$attribute)) $attributes->enable($attribute);
		}
		//$attributes->enable("*");
		$attributes->append($doc, $extensions);
		
		$privacyNotice = $doc->createElementNS('http://schemas.xmlsoap.org/ws/2005/05/identity', 'ic:PrivacyNotice');
		$privacyNotice->setAttribute("Version", "1");
		$privacyNotice->appendChild($doc->createTextNode('http://localhost:8888/auth/privacy'));
		$extensions->appendChild($privacyNotice);
		
		$authnRequest->appendChild($extensions);
		$doc->appendChild($authnRequest);
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		return base64_encode($doc->saveXML());
	}
	
	function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	
				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),
	
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,
	
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,
	
				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
}