<?php

class shipping_Relay
{
	
	public $ref;
	public $distance;
	public $name;
	public $addressLine1;
	public $addressLine2;
	public $addressLine3;
	public $locationHint;
	public $zipCode;
	public $city;
	public $countryCode;
	public $longitude;
	public $latitude;
	public $mapUrl;
	public $pictureUrl;
	public $openingHours;
	
	/**
	 * @return field_type
	 */
	public function getRef()
	{
		return $this->ref;
	}
	
	/**
	 * @return field_type
	 */
	public function getDistance()
	{
		return $this->distance;
	}
	
	/**
	 * @return field_type
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return field_type
	 */
	public function getAddressLine1()
	{
		return $this->addressLine1;
	}
	
	/**
	 * @return field_type
	 */
	public function getAddressLine2()
	{
		return $this->addressLine2;
	}
	
	/**
	 * @return field_type
	 */
	public function getAddressLine3()
	{
		return $this->addressLine3;
	}
	
	/**
	 * @return field_type
	 */
	public function getLocationHint()
	{
		return $this->locationHint;
	}
	
	/**
	 * @return field_type
	 */
	public function getZipCode()
	{
		return $this->zipCode;
	}
	
	/**
	 * @return field_type
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @return field_type
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}
	
	/**
	 * @return field_type
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}
	
	/**
	 * @return field_type
	 */
	public function getMapUrl()
	{
		return $this->mapUrl;
	}
	
	/**
	 * @return field_type
	 */
	public function getPictureUrl()
	{
		return $this->pictureUrl;
	}
	
	/**
	 * @return field_type
	 */
	public function getOpeningHours()
	{
		return $this->openingHours;
	}
	
	/**
	 * @param field_type $ref
	 */
	public function setRef($ref)
	{
		$this->ref = $ref;
	}
	
	/**
	 * @param field_type $distance
	 */
	public function setDistance($distance)
	{
		$this->distance = $distance;
	}
	
	/**
	 * @param field_type $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @param field_type $addressLine1
	 */
	public function setAddressLine1($addressLine1)
	{
		$this->addressLine1 = $addressLine1;
	}
	
	/**
	 * @param field_type $addressLine2
	 */
	public function setAddressLine2($addressLine2)
	{
		$this->addressLine2 = $addressLine2;
	}
	
	/**
	 * @param field_type $addressLine3
	 */
	public function setAddressLine3($addressLine3)
	{
		$this->addressLine3 = $addressLine3;
	}
	
	/**
	 * @param field_type $locationHint
	 */
	public function setLocationHint($locationHint)
	{
		$this->locationHint = $locationHint;
	}
	
	/**
	 * @param field_type $zipCode
	 */
	public function setZipCode($zipCode)
	{
		$this->zipCode = $zipCode;
	}
	
	/**
	 * @param field_type $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}
	
	/**
	 * @param field_type $longitude
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
	}
	
	/**
	 * @param field_type $latitude
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
	}
	
	/**
	 * @param field_type $mapUrl
	 */
	public function setMapUrl($mapUrl)
	{
		$this->mapUrl = $mapUrl;
	}
	
	/**
	 * @param field_type $pictureUrl
	 */
	public function setPictureUrl($pictureUrl)
	{
		$this->pictureUrl = $pictureUrl;
	}
	
	/**
	 * @param field_type $openingHours
	 */
	public function setOpeningHours($openingHours)
	{
		$this->openingHours = $openingHours;
	}
	
	/**
	 * @return field_type
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}
	
	/**
	 * @param field_type $countryCode
	 */
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
	}
	
	public function getAddress()
	{
		
		$string = $this->getName() . PHP_EOL;
		$string .= $this->getAddressLine1() . PHP_EOL;
		if ($this->getAddressLine2())
		{
			$string .= $this->getAddressLine2() . PHP_EOL;
		}
		if ($this->getAddressLine3())
		{
			$string .= $this->getAddressLine3() . PHP_EOL;
		}
		$string .= $this->getZipCode() . ' ' . $this->getCity() . PHP_EOL;
		return $string;
	
	}
	
	public function getAddressAsHtml()
	{
		return f_util_HtmlUtils::textToHtml($this->getAddress());
	}
	
	public function hasCoordinate()
	{
		if ($this->longitude != null && $this->latitude != null)
		{
			return true;
		}
		return false;
	}

}

?>