<?php

require_once(__DIR__ . '/db.php');

class DbCourses extends DB
{
    public function __construct()
    {
        parent::__construct();
    }


    public function db_close()
    {
        if (isset($mysqli)) $mysqli->close();
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




    public function loadCourseExport($p_trainer,
                                     $p_zeitraum,
                                     $p_begin,
                                     $p_end,
                                     $p_location,
                                     $p_category,
                                     $p_subcategory,
                                     $p_status,
                                     $p_publishing,
                                     $p_course_number,
                                     $p_student_email,
                                     $p_course_format, // new
                                     $p_course_type,
                                     $p_course_level,
                                     $p_student_name,    //new
                                     $p_student_lastname)   //new
    {


        if (!($p_trainer == $_SESSION["user_id"] ||
            $_SESSION["user_is_organizer"] == "1" ||
            $_SESSION["user_is_admin"] == "1")
        ) {
            echo "E-2221: No Permission to read Data. Ask your Administrator";
            return;
        }

        $begin1 = DateTime::createFromFormat('d.m.Y', $p_begin);
        if (!$begin1) {
            $p_begin = "01.01.1980";
        }
        $end1 = DateTime::createFromFormat('d.m.Y', $p_end);
        if (!$end1) {
            $p_end = "01.01.2199";
        }
        $p_course_number = trim($p_course_number, ',');
        if (!(isset($p_course_number) && is_numeric(str_replace(',', '', $p_course_number)))) {
            $p_course_number = -1;
        }
        if (empty($p_student_email)) {
            $p_student_email = "all";
        }
        if (empty($p_student_name)) {
            $p_student_name = "all";
        }
        if (empty($p_student_lastname)) {
            $p_student_lastname = "all";
        }
//        $db = $this->db_connect();

//        $db->query("SET SESSION group_concat_max_len = 3000;");
        $statement =

            "SELECT 
						c.course_id as course_id,
						cf.name as course_format_name,
						ct.name as course_type_name,
						cl.name as course_level_name,
						c.name as kursname,
						s.prename as prename,
						s.surname as surname,
						s.email as email,
						c.public_remark as public_remark,
						c.private_remark as private_remark,
						c.begin as begin,
						c.end as end,
						r.status as status,
						r.price_payed as price_payed,
						cl.price as price,
						cl.member_price as price_member
				   from
							  as_courses       c
				   left join as_course_formats cf			on c.course_format_id = cf.id
				   left join as_course_types	  ct			on c.course_type_id = ct.id
				   left join as_course_levels  cl			on c.course_level_id = cl.id
				   inner join as_users           u			on c.trainer_id = u.user_id
				   inner join as_locations       l			on c.location_id = l.location_id
				   left  join as_categories      ca			on c.cat_id = ca.cat_id
				   left  join as_users           u2			on c.trainer_id2 = u2.user_id
				   left  join as_registrations   r 			on c.course_id = r.course_id
				   left  join as_students        s			on r.student_id = s.student_id
				  where
				     (c.course_id IN(" . $p_course_number . ")
				       AND (
				       		" . $_SESSION['user_is_organizer'] . " = 1
				       		OR " . $_SESSION["user_is_admin"] . " = 1
							OR " . $_SESSION["user_id"] . " = c.trainer_id
							OR " . $_SESSION["user_id"] . " = c.trainer_id2
					   		)

					 )
				   	 OR
				   	 (
				   	 	'$p_course_number' = -1
				   	 	AND
				   	    ('$p_student_email' = 'all'
				   	     		or EXISTS(SELECT 1 FROM as_students s2
				   	     				   inner join as_registrations r2 on r2.student_id = s2.student_id
										   WHERE s2.email LIKE '%$p_student_email%'
										     AND r2.course_id = c.course_id))
						AND
				   	    ('$p_student_name' = 'all'
				   	     		or EXISTS(SELECT 1 FROM as_students s2
				   	     				   inner join as_registrations r2 on r2.student_id = s2.student_id
										   WHERE s2.prename LIKE '%$p_student_name%'
										     AND r2.course_id = c.course_id))
						AND
				   	    ('$p_student_lastname' = 'all'
				   	     		or EXISTS(SELECT 1 FROM as_students s2
				   	     				   inner join as_registrations r2 on r2.student_id = s2.student_id
										   WHERE s2.surname LIKE '%$p_student_lastname%'
										     AND r2.course_id = c.course_id))
						and
						('$p_trainer' = 'all'
								or '$p_trainer' = c.trainer_id
								or '$p_trainer' = c.trainer_id2)
						and
						( 		('$p_zeitraum'='all')
							OR
								('$p_zeitraum'='aktuell' and

										now() - INTERVAL 1 DAY   < c.end
								)
							OR
								('$p_zeitraum'='ab_heute' and
										date(now()) < c.begin
								)
							OR
								('$p_zeitraum'='von_bis' and

										(	STR_TO_DATE('$p_begin', '%d.%m.%Y') <= c.begin
											and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY >= c.end )
								)
						)
						and  ('$p_location' = 'all'
								or '$p_location' = c.location_id)
						and  ('$p_category' = 'all'
								or '$p_category' = c.cat_id)
						and  ('$p_subcategory' = 'all'
								or '$p_subcategory' = c.subcat_id)
						and
						(	 ($p_status = -1 AND
								c.status IN (1,2,3))

							OR $p_status = -2

							OR $p_status =  COALESCE(c.status, '0')
						)
						and
						(	$p_publishing = -1
								OR
							$p_publishing  = COALESCE(c.publishing, '0')
						)
						and  ('$p_course_format' = 'all'
								or '$p_course_format' = cf.id)
						and  ('$p_course_type' = 'all'
								or '$p_course_type' = ct.id)
						and  ('$p_course_level' = 'all'
								or '$p_course_level' = cl.id)
					)
			     ORDER BY c.begin
				 LIMIT 2000;";


        return $this->getReadResultObject($statement);

    }
}

?>