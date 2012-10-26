<?

class test_controller extends Controller {
	
	public function test() {
		$fb = new Facebook;
		$fb->connect();
	}
	
}