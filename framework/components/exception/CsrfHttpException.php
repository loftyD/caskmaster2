<?php
namespace components\exception;
class CsrfHttpException extends HttpException
{

	/**
	 * Constructor.
	 * @param integer $status HTTP status code, such as 404, 500, etc.
	 * @param string $message error message
	 * @param integer $code error code
	 */
	public function __construct($message=null)
	{
		$_SERVER['app']->map('error', function(CsrfHttpException $ex) use($message) {
		$html = "";
		if($message == null)
		{
			$message = "Resource Not Found";
		}
		$html .= "<div class=\"container\"><h1>Error</h1><p>$message</p></div>";
		$_SERVER['app']->render("error/foot", array("body" => $html));
		});
	}
}
