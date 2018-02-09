<?php

require_once __DIR__ . '/db.php';
require_once dirname(dirname(__DIR__)) . '/kbs/booking/_v2/inc/df.php';

class DbCourse extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getReadResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->getResults($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
                $result->data = $res;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getWriteResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->db->query($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getErrorResultObject($errtxt)
    {
        $result = new Result();
        $result->errtxt = $errtxt;
        return $result;
    }

    public function getFormats()
    {
        // $p = $this->processParameter($parameter);
        // $p->filter_name = $this->db->real_escape_string($parameter['filter_name']);

        $sql = "
			SELECT 
						 cf.id AS course_format_id,
						 cf.name AS course_format_name
			  FROM as_courses c
			 INNER JOIN as_course_levels cl ON c.course_level_id = cl.id
			 INNER JOIN as_course_types ct ON cl.course_type_id = ct.id
			 INNER JOIN as_course_formats cf ON ct.course_format_id = cf.id
			 INNER JOIN as_locations l ON c.location_id = l.location_id
			 INNER JOIN as_users t ON c.trainer_id = t.user_id
			 WHERE c.status IN (2,3)
			   AND c.end + INTERVAL 1 DAY > now()
				 AND c.publishing = 2
			 GROUP BY cf.id, cf.name
			 ORDER BY cf.sort_no, cf.name, ct.sort_no, ct.name, cl.sort_no, cl.name, c.name
			  ";
        return $this->getReadResultObject($sql);
    }

    public function getLevels($course_format_id)
    {
        //$p = $this->processParameter($parameter);
        // $p->filter_name = $this->db->real_escape_string($parameter['filter_name']);

        $course_format_id = $this->db->real_escape_string($course_format_id);
        $sql = "
			SELECT 
						 c.course_level_id,
						 cl.name as course_level_name,
						 ct.name as course_type_name,
						 cf.name as course_format_name,
						 cl.description,
						 cl.units,
						 cl.price,
						 cl.member_price
			  FROM as_courses c
			 INNER JOIN as_course_levels cl ON c.course_level_id = cl.id
			 INNER JOIN as_course_types ct ON cl.course_type_id = ct.id
			 INNER JOIN as_course_formats cf ON ct.course_format_id = cf.id
			 INNER JOIN as_locations l ON c.location_id = l.location_id
			 INNER JOIN as_users t ON c.trainer_id = t.user_id
			  LEFT JOIN as_users t2 ON c.trainer_id2 = t.user_id
			 WHERE c.status IN (2,3)
			   AND c.end + INTERVAL 1 DAY > now()
				 AND c.publishing = 2
			   AND cf.id = $course_format_id
			 GROUP BY cl.id, cl.name
			 ORDER BY cf.sort_no, cf.name, ct.sort_no, ct.name, cl.sort_no, cl.name, c.name
			  ";
        return $this->getReadResultObject($sql);
    }

    public function getCourses($course_level_id)
    {
        //$p = $this->processParameter($parameter);
        // $p->filter_name = $this->db->real_escape_string($parameter['filter_name']);

        $course_level_id = $this->db->real_escape_string($course_level_id);

        $sql = "
			SELECT c.course_id,
						 c.name,
						 c.price,
						 c.duration,
						 cf.id as course_format_id,
						 ct.id as course_type_id,
						 cl.id as course_level_id,
						 c.registration_code,
						 c.not_on,
						 cf.name as course_format_name,
						 ct.name as course_type_name,
						 cl.name as course_level_name,
						 l.short_name as location_name,
						 t.user_id as trainer1_id,
						 t2.user_id as trainer2_id,
						 t.prename as trainer1_name,
						 t2.prename as trainer2_name,
						 date_format(c.date1, '%d.%m.%y') as date1,
						 date_format(c.end, '%d.%m.%y') as end,
						 date_format(c.date1, '%w') as weekday,
						 date_format(c.date1, '%H:%i') as time_begin,
						 date_format(c.date1_end, '%H:%i') as time_end,
						 (c.max_count - c.pre_reg_count - c.registration_count) as free_places_count,
                                                 if(c.date2 IS NULL, null, date_format(c.date2, '%H:%i')) as time_begin2,
                                                 if(c.date3 IS NULL, null, date_format(c.date3, '%H:%i')) as time_begin3,
                                                 if(c.date4 IS NULL, null, date_format(c.date4, '%H:%i')) as time_begin4,
                                                 if(c.date5 IS NULL, null, date_format(c.date5, '%H:%i')) as time_begin5,
                                                 if(c.date6 IS NULL, null, date_format(c.date6, '%H:%i')) as time_begin6,
                                                 if(c.date7 IS NULL, null, date_format(c.date7, '%H:%i')) as time_begin7,
                                                 if(c.date8 IS NULL, null, date_format(c.date8, '%H:%i')) as time_begin8,
                                                 if(c.date9 IS NULL, null, date_format(c.date9, '%H:%i')) as time_begin9,
                                                 if(c.date10 IS NULL, null, date_format(c.date10, '%H:%i')) as time_begin10,
                                                 if(c.date11 IS NULL, null, date_format(c.date11, '%H:%i')) as time_begin11,
                                                 if(c.date12 IS NULL, null, date_format(c.date12, '%H:%i')) as time_begin12,
                                                 if(c.date2_end IS NULL, null, date_format(c.date2_end, '%H:%i')) as time_end2,
                                                 if(c.date3_end IS NULL, null, date_format(c.date3_end, '%H:%i')) as time_end3,
                                                 if(c.date4_end IS NULL, null, date_format(c.date4_end, '%H:%i')) as time_end4,
                                                 if(c.date5_end IS NULL, null, date_format(c.date5_end, '%H:%i')) as time_end5,
                                                 if(c.date6_end IS NULL, null, date_format(c.date6_end, '%H:%i')) as time_end6,
                                                 if(c.date7_end IS NULL, null, date_format(c.date7_end, '%H:%i')) as time_end7,
                                                 if(c.date8_end IS NULL, null, date_format(c.date8_end, '%H:%i')) as time_end8,
                                                 if(c.date9_end IS NULL, null, date_format(c.date9_end, '%H:%i')) as time_end9,
                                                 if(c.date10_end IS NULL, null, date_format(c.date10_end, '%H:%i')) as time_end10,
                                                 if(c.date11_end IS NULL, null, date_format(c.date11_end, '%H:%i')) as time_end11,
                                                 if(c.date12_end IS NULL, null, date_format(c.date12_end, '%H:%i')) as time_end12
			  FROM as_courses c
			 INNER JOIN as_course_levels cl ON c.course_level_id = cl.id
			 INNER JOIN as_course_types ct ON cl.course_type_id = ct.id
			 INNER JOIN as_course_formats cf ON ct.course_format_id = cf.id
			 INNER JOIN as_locations l ON c.location_id = l.location_id
			 INNER JOIN as_users t ON c.trainer_id = t.user_id
			  LEFT JOIN as_users t2 ON c.trainer_id2 = t2.user_id
			 WHERE c.status IN (2,3)
			   AND c.end + INTERVAL 1 DAY > now()
				 AND c.publishing = 2
			   AND cl.id = $course_level_id
			 ORDER BY c.date1, c.name
			  ";

        // if( ! empty($parameter['filter_name'])) {
        // $sql .= "
        // AND name LIKE '%$p->filter_name%' ";
        // }
        // if( $parameter['filter_status'] != -2) {
        // $sql .= "
        // AND status = $p->filter_status ";
        // }
        // $sql .= "
        // ORDER BY cf.sort_no, cf.name, ct.sort_no, ct.name, cl.sort_no, cl.name, c.name
        // ";
        // echo $sql;
        return $this->getReadResultObject($sql);
    }

    public function getDetails($parameter)
    {
        $p = $this->processParameter($parameter);
        $p->filter_name = $this->db->real_escape_string($parameter['filter_name']);

        $course_level_id = $this->db->real_escape_string($course_level_id);

        $sql = "
			SELECT c.course_id,
						 c.name,
                                                 c.course_id,
						 c.price,
						 c.registration_code,
						 c.not_on,
						 cl.name as course_level_name,
						 cf.name as course_format_name,
						 l.short_name as location_name,
						 t.prename as trainer_name1,
						 t2.prename as trainer_name2,
						 date_format(c.date1, '%d.%m.%Y') as begin,
						 date_format(c.end, '%d.%m.%Y') as end,                                                 
                                                 if(c.date1 IS NULL, null, date_format(c.date1, '%W, %d.%m. %H:%i - ')) as date1,
                                                 if(c.date2 IS NULL, null, date_format(c.date2, '%W, %d.%m. %H:%i - ')) as date2,
                                                 if(c.date3 IS NULL, null, date_format(c.date3, '%W, %d.%m. %H:%i - ')) as date3,
                                                 if(c.date4 IS NULL, null, date_format(c.date4, '%W, %d.%m. %H:%i - ')) as date4,
                                                 if(c.date5 IS NULL, null, date_format(c.date5, '%W, %d.%m. %H:%i - ')) as date5,
                                                 if(c.date6 IS NULL, null, date_format(c.date6, '%W, %d.%m. %H:%i - ')) as date6,
                                                 if(c.date7 IS NULL, null, date_format(c.date7, '%W, %d.%m. %H:%i - ')) as date7,
                                                 if(c.date8 IS NULL, null, date_format(c.date8, '%W, %d.%m. %H:%i - ')) as date8,
                                                 if(c.date9 IS NULL, null, date_format(c.date9, '%W, %d.%m. %H:%i - ')) as date9,
                                                 if(c.date10 IS NULL, null, date_format(c.date10, '%W, %d.%m. %H:%i - ')) as date10,
                                                 if(c.date11 IS NULL, null, date_format(c.date11, '%W, %d.%m. %H:%i - ')) as date11,
                                                 if(c.date12 IS NULL, null, date_format(c.date12, '%W, %d.%m. %H:%i - ')) as date12,
                                                 Concat
                                                        (
                                                                date_format(c.date1, '%d.%m'),
                                                                if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
                                                                if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
                                                                if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
                                                                if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
                                                                if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
                                                                if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
                                                                if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m.')),
                                                                if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m.')),
                                                                if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.')),
                                                                if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m.')),
                                                                if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m.'))
                                                        ) as date_listing,
						Concat
							(
								(CASE date_format(c.date1, '%w')
									WHEN 1 THEN 'Mo'
									WHEN 2 THEN 'Di'
									WHEN 3 THEN 'Mi'
									WHEN 4 THEN 'Do'
									WHEN 5 THEN 'Fr'
									WHEN 6 THEN 'Sa'
									WHEN 0 THEN 'So'
								END),
								', ',
								date_format(c.date1, '%d.%m.%Y')
							)	as begin_with_weekday,
						 cl.price,
						 cl.member_price,
                                                 c.precondition,
						 date_format(c.date1, '%w') as weekday,
						 date_format(c.date1, '%H:%i') as time_begin,
                                                 if(c.date2 IS NULL, null, date_format(c.date2, '%H:%i')) as time_begin2,
                                                 if(c.date3 IS NULL, null, date_format(c.date3, '%H:%i')) as time_begin3,
                                                 if(c.date4 IS NULL, null, date_format(c.date4, '%H:%i')) as time_begin4,
                                                 if(c.date5 IS NULL, null, date_format(c.date5, '%H:%i')) as time_begin5,
                                                 if(c.date6 IS NULL, null, date_format(c.date6, '%H:%i')) as time_begin6,
                                                 if(c.date7 IS NULL, null, date_format(c.date7, '%H:%i')) as time_begin7,
                                                 if(c.date8 IS NULL, null, date_format(c.date8, '%H:%i')) as time_begin8,
                                                 if(c.date9 IS NULL, null, date_format(c.date9, '%H:%i')) as time_begin9,
                                                 if(c.date10 IS NULL, null, date_format(c.date10, '%H:%i')) as time_begin10,
                                                 if(c.date11 IS NULL, null, date_format(c.date11, '%H:%i')) as time_begin11,
                                                 if(c.date12 IS NULL, null, date_format(c.date12, '%H:%i')) as time_begin12,
						 date_format(c.date1_end, '%H:%i') as time_end,
                                                 if(c.date2_end IS NULL, null, date_format(c.date2_end, '%H:%i')) as time_end2,
                                                 if(c.date3_end IS NULL, null, date_format(c.date3_end, '%H:%i')) as time_end3,
                                                 if(c.date4_end IS NULL, null, date_format(c.date4_end, '%H:%i')) as time_end4,
                                                 if(c.date5_end IS NULL, null, date_format(c.date5_end, '%H:%i')) as time_end5,
                                                 if(c.date6_end IS NULL, null, date_format(c.date6_end, '%H:%i')) as time_end6,
                                                 if(c.date7_end IS NULL, null, date_format(c.date7_end, '%H:%i')) as time_end7,
                                                 if(c.date8_end IS NULL, null, date_format(c.date8_end, '%H:%i')) as time_end8,
                                                 if(c.date9_end IS NULL, null, date_format(c.date9_end, '%H:%i')) as time_end9,
                                                 if(c.date10_end IS NULL, null, date_format(c.date10_end, '%H:%i')) as time_end10,
                                                 if(c.date11_end IS NULL, null, date_format(c.date11_end, '%H:%i')) as time_end11,
                                                 if(c.date12_end IS NULL, null, date_format(c.date12_end, '%H:%i')) as time_end12,
						 (c.max_count - c.pre_reg_count - c.registration_count) as free_places_count,
						 c.textblock_mode as textblock_mode,
						 c.textblock as textblock
			  FROM as_courses c
			 INNER JOIN as_course_levels cl ON c.course_level_id = cl.id
			 INNER JOIN as_course_types ct ON cl.course_type_id = ct.id
			 INNER JOIN as_course_formats cf ON ct.course_format_id = cf.id
			 INNER JOIN as_locations l ON c.location_id = l.location_id
			 INNER JOIN as_users t ON c.trainer_id = t.user_id
			  LEFT JOIN as_users t2 ON c.trainer_id2 = t2.user_id
			 WHERE c.status IN (2,3)
			   AND c.end + INTERVAL 1 DAY > now()
				 AND c.publishing = 2
			   AND c.course_id = $p->course_id
			   AND c.registration_code = $p->registration_code
			 ORDER BY c.date1, c.name
			  ";

        // echo $sql;
        return $this->getReadResultObject($sql);
    }

    public function registrateMembership($parameter)
    {
        // $p = $this->processParameter($parameter);

        // #########################################

        if (rb_empty($parameter['prename'])) return $this->getErrorResultObject('Bitte Vornamen angeben.');
        if (rb_empty($parameter['surname'])) return $this->getErrorResultObject('Bitte Nachnamen angeben.');
        // if(rb_empty($parameter['email'])) return $this->getErrorResultObject('Bitte Email angeben.');
        if (!filter_var(/*$p_email*/
            $parameter['email'], FILTER_VALIDATE_EMAIL)
        ) return $this->getErrorResultObject('Bitte gültige Email angeben.');

        if (rb_empty($parameter['phone'])) return $this->getErrorResultObject('Bitte Telefonnummer angeben.');
        if (rb_empty($parameter['birthday'])) return $this->getErrorResultObject('Bitte Geburtsdatum angeben.');
        if (rb_empty($parameter['street'])) return $this->getErrorResultObject('Bitte Straße und Hausnummer angeben.');
        if (rb_empty($parameter['zip'])) return $this->getErrorResultObject('Bitte PLZ angeben.');
        if (rb_empty($parameter['city'])) return $this->getErrorResultObject('Bitte Stadt angeben.');
        if (rb_empty($parameter['terms_accepted'])) return $this->getErrorResultObject('Für die Anmeldung müssen die AGB akzeptiert werden.');

        $p = $this->processParameter($parameter);

        $birthday = df::formatDateFromPattern($parameter['birthday'], 'd.m.Y', 'Y-m-d');
        if (empty($birthday)) {
            return $this->getErrorResultObject('Bitte Geburtsdatum angeben.');
        } else {
            $p->birthday = "'" . $birthday . "'";
        }

        // ##########################################

        $sql = "
		  INSERT INTO as_membership_registrations
		  	 SET prename = $p->prename,
	  	 		 surname = $p->surname,
	  	 		 email = $p->email,
	  	 		 phone = $p->phone,
	  	 		 birthday = $p->birthday,
	  	 		 street = $p->street,
	  	 		 zip = $p->zip,
	  	 		 city = $p->city;";
        // echo $sql;
        return $this->getWriteResultObject($sql);
    }


}

?>