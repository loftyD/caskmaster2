<?php
namespace components\exception;
class HttpException extends \Exception
{

	/**
	 * Constructor.
	 * @param integer $status HTTP status code, such as 404, 500, etc.
	 * @param string $message error message
	 * @param integer $code error code
	 */
	public function __construct($message=null)
	{
		$_SERVER['app']->map('error', function(HttpException $ex) use($message) {

		if($message == null)
		{
			$message = "Resource Not Found";
		}
		
		$_SERVER['app']->render("error/head");
		$html = '<div class="container">';
		$html .= "<h1>Error 404</h1><p>$message</p>";
		$html .= '</div>';
		$_SERVER['app']->render("error/foot", array("body" => $html));

		});
	}

}