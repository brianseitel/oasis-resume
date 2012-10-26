<?

class Book extends Object {
	
	public $table = 'books';

	public static function import() {
		$client = new AmazonECS('AKIAJIKDUDIAXIJ6NVBA', 'B9djYhewotdDCEtCB2J5hPiSsaehR+N4wWXZ+To+', 'com', 'wheoftimdat-20');
		$client->category('Books');
		$client->returnType(AmazonECS::RETURN_TYPE_ARRAY);
		$response = $client->country('com')
						->responseGroup('Images,Large')
						->optionalParameters(array('BrowseNode' => 283155))
						->search('');

		$results = fetch($response, 'Items');

		foreach ($results['Item'] as $result) {
			$book = new Book;
			$book->title = fetch($result['ItemAttributes'], 'Title');
			$book->referral_link = fetch($result, 'DetailPageUrl');
			$book->image = fetch($result['LargeImage'], 'URL');
			$book->isbn = fetch($result['ItemAttributes'], 'ISBN');
			$book->save();
		}
	}

	public function genres() {
		$sql = 'SELECT id FROM genres WHERE book_id = '.$this->id;
		$genre_ids = DB::getArray($sql);

		return $genre_ids;
	}
}