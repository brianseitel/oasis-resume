<?

class User extends Object {
	
	var $table = 'users';

	public function books() {
		$sql = 'SELECT book_id FROM user_books WHERE user_id = '.$this->id;
		$book_ids = DB::getArray($sql);

		$books = array();
		foreach ($book_ids as $book_id)
			$books[] = new Book($book_id);

		return $books;
	}
}