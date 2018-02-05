<?php
namespace components;

class EasyCSRFExtended extends \EasyCSRF\EasyCSRF {

	/**
	 * Check the CSRF token is valid
	 *
	 * @param  string  $key            Key for this token
	 * @param  string  $token          The token string (usually found in $_POST)
	 * @param  int     $timespan       Makes the token expire after $timespan seconds (null = never)
	 * @param  boolean $multiple       Makes the token reusable and not one-time (Useful for ajax-heavy requests)
	 */
	public function check($key, $token, $timespan = null, $multiple = false)
	{
		$key = $result = preg_replace('/[^a-zA-Z0-9]+/', '', $key);

		if (!$token) {
			throw new \components\exception\CsrfHttpException('Missing CSRF form token.');
		}

		$session_token = $this->session->get($this->session_prefix . $key);
		if (!$session_token) {
			throw new \components\exception\CsrfHttpException('Missing CSRF session token.');
		}

		if (!$multiple) {
			$this->session->set($this->session_prefix . $key, null);
		}

		if (sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) != substr(base64_decode($session_token), 10, 40)) {
			throw new \components\exception\CsrfHttpException('Form origin does not match token origin.');
		}

		if ($token != $session_token) {
			throw new \components\exception\CsrfHttpException('Invalid CSRF token.');
		}

		// Check for token expiration
		if ($timespan != null && is_int($timespan) && intval(substr(base64_decode($session_token), 0, 10)) + $timespan < time()) {
			throw new \components\exception\CsrfHttpException('CSRF token has expired.');
		}
	}
}