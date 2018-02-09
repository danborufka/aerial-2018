<?php

	function rb_empty($value) {
		if(	empty($value) &&
				$value !== '0' &&
				$value !== 0)
		{
			return true;
		}else {
			return false;
		}
	}

?>