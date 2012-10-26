<?

class LinkedIn {
	
	const API_KEY = 'b91b2sx8y225';
	const API_SECRET = 'Blt9fNzFpZYSmJZP';
	const OAUTH_TOKEN = '497fca2e-d769-4f89-922f-65f4f8aeee03';
	const OAUTH_SECRET = '76b0b927-d910-45d1-84fd-509970fc7929';

	public function connect() {
		$oauthstate = fetch($_COOKIE, 'linkedinoauthstate');
		$to = new LinkedInOAuth(self::API_KEY, self::API_SECRET);

		$maxretrycount = 1;
		$retrycount = 0;
		while ($retrycount<$maxretrycount) {
			$tok = $to->getRequestToken();
			if (isset($tok['oauth_token']) && isset($tok['oauth_token_secret']))
				break;

			$retrycount += 1;
			sleep($retrycount*5);
		}

		$tokenpublic = $tok['oauth_token'];
		$tokenprivate = $tok['oauth_token_secret'];
		$state = 'start';

		// Create a new set of information, initially just containing the keys we need to make
		// the request.
		$oauthstate = array(
			'request_token' => $tokenpublic,
			'request_token_secret' => $tokenprivate,
			'access_token' => '',
			'access_token_secret' => '',
			'state' => $state,
		);

		setcookie('linkedinoauthstate', $state);

		if (isset($tok['oauth_token']) && ($oauthstate['access_token']=='')) {
        	pp($tok);
    
			$urlaccesstoken = $tok['oauth_token'];
			$urlaccessverifier = $tok['oauth_verifier'];
			pp("Found access tokens in the URL - $urlaccesstoken, $urlaccessverifier");

			$requesttoken = $oauthstate['request_token'];
			$requesttokensecret = $oauthstate['request_token_secret'];

			pp("Creating API with $requesttoken, $requesttokensecret");	

			$to = new LinkedInOAuth(
				self::API_KEY,
				self::API_SECRET,
				$requesttoken,
				$requesttokensecret
			);

			$tok = $to->getAccessToken($urlaccessverifier);

			$accesstoken = $tok['oauth_token'];
			$accesstokensecret = $tok['oauth_token_secret'];

			pp("Calculated access tokens $accesstoken, $accesstokensecret");	

			$oauthstate['access_token'] = $accesstoken;
			$oauthstate['access_token_secret'] = $accesstokensecret;
			$oauthstate['state'] = 'done';

			setcookie('linkedinoauthstate', 'done');	
		}

		$accesstoken = $oauthstate['access_token'];
        $accesstokensecret = $oauthstate['access_token_secret'];

        $to = new LinkedInOAuth(
            self::API_KEY,
            self::API_SECRET,
            $accesstoken,
            $accesstokensecret
        );
        
        $profile_result = $to->oAuthRequest('http://api.linkedin.com/v1/people/~');
        $profile_data = simplexml_load_string($profile_result);

        pd(htmlspecialchars($profile_data, 1));
	}

}