<?

class layouts_controller extends Controller {
	
	public function application() {
		$this->controller = fetch($_GET, 'controller');
		$this->view = fetch($_GET, 'view');
	}
}