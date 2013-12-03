<?php

class Request
{
	public static function getParam($name)
	{
		if (isset($_POST[$name])) {
			return $_POST[$name];
		} else {
			return null;
		}
	}
}