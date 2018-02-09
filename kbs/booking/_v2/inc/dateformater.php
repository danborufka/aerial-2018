<?php
	class dateFormater {
	
	   static function formatDateFromPattern($dateString, $patternFrom, $patternTo)
	   {
	       if(empty($dateString) || empty($patternFrom) || empty($patternTo)) return NULL;
			
			if($patternFrom == 'H:i' && strlen($dateString) <= 2) $patternFrom = 'H';
			
	       $date_time = @DateTime::createFromFormat($patternFrom, $dateString);
	       if(!$date_time) return NULL;
	       $result = @date($patternTo, $date_time->getTimeStamp());
	
	       return !$result ? NULL : $result;
	   }
	   static function getTimeStampFromPattern($dateString, $pattern)
	   {
	       if(empty($dateString)) return NULL;
	       $date_time = @DateTime::createFromFormat($pattern, $dateString);
	       if(!$date_time) return NULL;
	       $result = $date_time->getTimeStamp();
	
	       return !$result ? NULL : $result;
	   }
	   static function compare($dateString1, $dateString2, $pattern1, $pattern2 = 'default')
	   {
	       if($pattern2 == 'default') $pattern2 = $pattern1;
	       $date_time1 = @DateTime::createFromFormat($pattern1, $dateString1);
	       $date_time2 = @DateTime::createFromFormat($pattern2, $dateString2);
	       if(!$date_time1) return NULL;
	       if(!$date_time2) return NULL;
	       $time1 = $date_time1->getTimeStamp();
	       $time2 = $date_time2->getTimeStamp();
	       if($time1 == $time2) {
	           return 0;
	       }elseif ($time1 < $time2) {
	           return -1;
	       }else {
	           return 1;
	       }
	   }
	}
?>