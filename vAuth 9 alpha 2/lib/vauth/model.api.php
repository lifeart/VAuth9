<?

	class vAPI {
		
		function __construct() {
		
		
		}
		
		function render($content='auth.htm') {
			global $f3;
			$f3->set('content',$content);
			echo View::instance()->render('layout.htm');
		}
	
	}