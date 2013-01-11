<?php 

class WPSUISSEID_Attributes {

	private $attributes = array(
		"email" =>
			array("enabled" => true,
			"id" => "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"
		),
		"origin" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/origin"
			),
		"identificationValidUntil" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/identificationValidUntil"
			),
		"identificationKind" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/identificationKind"
			),
		"surname" =>
			array("enabled" => false,
			"id" => "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname"
			),
		"identificationIssuedOn" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/identificationIssuedOn"
			),
		"issuingCountry" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/issuingCountry"
			),
		"gender" =>
			array("enabled" => false,
			"id" => "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/gender"
			),
		"dateOfBirthPartiallyKnown" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/dateOfBirthPartiallyKnown"
			),
		"identificationNumber" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/identificationNumber"
			),
		"givenNames" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/givenNames"
			),
		"dateofbirth" =>
			array("enabled" => false,
			"id" => "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/dateofbirth"
			),
		"issuingOffice" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/issuingOffice"
			),
		"nationality" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/nationality"
			),
		"givenname" =>
			array("enabled" => false,
			"id" => "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname"
			),
		"placeOfBirth" =>
			array("enabled" => false,
			"id" => "http://www.ech.ch/xmlns/eCH-0113/1/placeOfBirth"
			)
	);
	
	public function append(&$doc, &$element) {
		foreach ($this->attributes as $attribute) {
			if (!$attribute['enabled']) continue;
			$x = $doc->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion', 'saml2:Attribute');
			$x->setAttribute("xmlns:eCH-0113", 'http://www.ech.ch/xmlns/eCH-0113/1');
			
			$x->setAttribute('Name', $attribute['id']);
			$x->setAttribute("eCH-0113:required", "true");
			$element->appendChild($x);
		}
	}
	
	public function enable($name = null ) {
		$this->toggle($name,true);
	}
	public function disable($name = null ) {
		$this->toggle($name,false);
	}
	
	private function toggle($name = null, $status = true) {
		if ($name == null || $name == "*"){
			foreach ( array_keys($this->attributes) as $attribute) {
				$this->attributes[$attribute]['enabled'] = $status;
			}
		} else {
			if (array_key_exists($name, $this->attributes)) {
				$this->attributes[$name]['enabled'] = $status;
			}
		}
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
}
?>