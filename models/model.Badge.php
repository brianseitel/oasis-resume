<?

class Badge extends Object {
	
	public static function check($genre_id) {
		$sql = 'SELECT DISTINCT(b.id) FROM books b
				INNER JOIN book_genre bg ON bg.book_id = b.id
				INNER JOIN genres g ON g.id = bg.genre_id
				WHERE g.id = '.$genre_id;
		$book_ids = DB::getArray($sql);

		$books = array();
		foreach ($book_ids as $book_id)
			$books[] = new Book($book_id);

		return $books;
	}
}