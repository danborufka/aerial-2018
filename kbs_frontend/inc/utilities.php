<?php
	class utilities {
		public static function getPostVal($key)
		{
			if (isset($_POST[$key]))
				return $_POST[$key];
			return "";
		}
		public static function output($res)
		{
			$output = json_encode($res);
			if (json_last_error() != 0) {
				$res = new Result();
				$res->error = 1;
				$res->errtxt = "JSON: " . json_last_error_msg();
				$res->errcode=1;
				$output = json_encode($res);
			}
			echo $output;
			exit();
		}
		public static function initParameter($parameterNames) {
			$parameter = array();
			foreach ($parameterNames as $parameterName) {
				$parameter[$parameterName] = utilities::getPostVal($parameterName);
			}
			return $parameter;
		}
	}
?>