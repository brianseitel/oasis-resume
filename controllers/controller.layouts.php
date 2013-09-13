<?

class layouts_controller extends Controller {
	
	public function application() {
		$this->controller = fetch($_GET, 'controller');
		$this->view = fetch($_GET, 'view');
		if (fetch($_GET, 'layout') == 'clean')
			die(render($this->controller, $this->view));
	}
}