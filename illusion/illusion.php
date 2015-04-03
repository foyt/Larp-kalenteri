<?php

  require __DIR__ . '/../vendor/autoload.php';
  require_once __DIR__ . '/config.php';
  
  class IllusionEventController {
  	
  	private $base_url;
  	private $client_id;
  	private $client_secret;
  	
  	public function __construct($base_url, $client_id, $client_secret) {
  		$this->base_url = $base_url;
  		$this->client_id = $client_id;
  		$this->client_secret = $client_secret;
  	}
  	
  	public function createEvent($published, $name, $description, $urlName, $joinMode, $signUpFee, $signUpFeeCurrency, $location, $ageLimit, $beginnerFriendly, $imageUrl, $typeId, $signUpStartDate, $signUpEndDate, $domain, $startDate, $endDate, $genreIds) {
  		$event = array(
  		  "published" => $published,
  			"name" => $name,
  			"description" => $description,
  			"urlName" => $urlName,
  			"joinMode" => $joinMode,
  			"signUpFee" => $signUpFee,
  			"signUpFeeCurrency" => $signUpFeeCurrency,
  		  "location" => $location,
  			"ageLimit" => $ageLimit,
  			"beginnerFriendly" => $beginnerFriendly,
  			"imageUrl" => $imageUrl,
  			"typeId" => $typeId,
  			"signUpStartDate" => (new DateTime())->setTimestamp(intval($signUpStartDate))->format('c'),
  			"signUpEndDate" => (new DateTime())->setTimestamp(intval($signUpEndDate))->format('c'),
  			"domain" => $domain,
  			"start" => (new DateTime())->setTimestamp(intval($startDate))->format('c'),
  			"end" => (new DateTime())->setTimestamp(intval($endDate))->format('c'),
  			"genreIds" => $genreIds
  		);
  		
  		$response = $this->createClient()->post("$this->base_url/rest/illusion/events", [
  	    'json' => $event
  		]);
  		
  		if ($response->getStatusCode() == 200) {
  			return $response->json();
  		}
  		
  		return null;
  	}
  	
  	public function updateEvent($id, $published, $name, $description, $urlName, $joinMode, $signUpFee, $signUpFeeCurrency, $location, $ageLimit, $beginnerFriendly, $imageUrl, $typeId, $signUpStartDate, $signUpEndDate, $domain, $startDate, $endDate, $genreIds) {
  		$event = array(
  		  "id" => $id,
  			"published" => $published,
  			"name" => $name,
  			"description" => $description,
  			"urlName" => $urlName,
  			"joinMode" => $joinMode,
  			"signUpFee" => $signUpFee,
  			"signUpFeeCurrency" => $signUpFeeCurrency,
  		  "location" => $location,
  			"ageLimit" => $ageLimit,
  			"beginnerFriendly" => $beginnerFriendly,
  			"imageUrl" => $imageUrl,
  			"typeId" => $typeId,
  			"signUpStartDate" => (new DateTime())->setTimestamp(intval($signUpStartDate))->format('c'),
  			"signUpEndDate" => (new DateTime())->setTimestamp(intval($signUpEndDate))->format('c'),
  			"domain" => $domain,
  			"start" => (new DateTime())->setTimestamp(intval($startDate))->format('c'),
  			"end" => (new DateTime())->setTimestamp(intval($endDate))->format('c'),
  			"genreIds" => $genreIds
  		);
  		
  		$response = $this->createClient()->put("$this->base_url/rest/illusion/events/$id", [
  	    'json' => $event
  		]);
  		
  		if ($response->getStatusCode() == 204) {
  			return $event;
  		}
  		
  		return null;
  	}
  	
  	public function getIllusionGenreIds($genres) {
  		$result = [];
  		
  		if (empty($genres)) {
  			return $result;
  		}
  		
  		$genreIdMap = $this->getIllusionGenreMap();
  		
  		foreach ($this->getGenreNames($genres) as $genreName) {
  			$result[] = $genreIdMap[$genreName];
  		}
  		
  		return $result;
  	}
  	
  	private function listIllusionGenres() {
  		$response = $this->createClient()->get("$this->base_url/rest/illusion/genres");
  		
  		if ($response->getStatusCode() == 200) {
  			return $response->json();
  		}
  		
  		return null;
  	}
  	
  	private function getIllusionGenreMap() {
  		$result = [];
  		
  	  foreach ($this->listIllusionGenres() as $illusionGenre) {
  	  	$result[$illusionGenre['name']] = $illusionGenre['id'];
  		}
  		
  		return $result;
  	}

  	private function getGenreNames($genres) {
  		$result = [];
  		
  		foreach ($genres as $genre) {
  			$result[] = $this->getGenreName($genre);
  		}
  		
  		return $result;
  	}
  	
  	private function getGenreName($genre) {
  		switch (trim($genre)) {
  			case "fantasy":
  			  return "Fantasia";
	  		case "scifi":
	  			return "Sci-fi";
  			case "cyberpunk":
  				return "Cyberpunk";
  			case "steampunk":
  				return "Steampunk";
  			case "postapo":
  				return "Post-apokalyptinen";
  			case "historical":
  				return "Historiallinen";
  			case "thriller":
  				return "Jännitys";
  			case "horror":
  				return "Kauhu";
  			case "reality":
  				return "Realismi";
  			case "city":
  				return "Kaupunkipeli";
  			case "newweird":
  				return "Uuskumma";
  			case "action":
  				return "Toiminta";
  			case "drama":
  				return "Draama";
  			case "humor":
  				return "Huumori";
  		}
  		
  		return null;
  	}

  	private function createClient() {
  		$handler = new GuzzleHttp\Ring\Client\StreamHandler();

  		$oauth2Client = new GuzzleHttp\Client([
  		  'handler' => $handler,
  			'base_url' => "$this->base_url/"
  		]);
  		
  		$config = [
  		  'client_id' => $this->client_id,
  		  'client_secret' => $this->client_secret
  		];
  		
  		$token = new CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials($oauth2Client, $config);
  		$refreshToken = new CommerceGuys\Guzzle\Oauth2\GrantType\RefreshToken($oauth2Client, $config);
  		$oauth2 = new CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber($token, $refreshToken);
  		$client = new GuzzleHttp\Client([
  		  'handler' => $handler,
  			'defaults' => [
  				'auth' => 'oauth2',
  				'subscribers' => [$oauth2]
 				]
  		]);
  		
  		return $client;
  	}
  	
  }
  
  function getIllusionClient() {
  	return new IllusionEventController(FNI_BASE_URL, FNI_CLIENT_ID, FNI_CLIENT_SECRET);
  }
  

?>