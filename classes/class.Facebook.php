<?

class Facebook {
	
	const API_KEY = '157168181000853';
	const API_SECRET = '95e52b177a5692ce0338ebd5e79e1416';

	public function connect() {
		$url = 'https://graph.facebook.com/brian.seitel';//?apikey='.API_KEY;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION , 1); // TRUE
		curl_setopt($ch, CURLOPT_HEADER ,0); // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); // RETURN THE CONTENTS OF THE CALL
		$results = curl_exec($ch);

		pd($results);
	}
}