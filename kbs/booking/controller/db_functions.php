<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();


class DB_Connect
{

    public function db_connect()
    {
        global $rb_configuration;
        $mysqli = new mysqli($rb_configuration->db_url, $rb_configuration->db_user, $rb_configuration->db_pwd, $rb_configuration->db_name, $rb_configuration->db_port);

        if ($mysqli->connect_error) {
            echo "E-1005: Fehler bei der Verbindung zur Datenbank.";
            exit();
        }
        if (!$mysqli->set_charset("utf8")) {
            echo "E-1006: Datenbankfehler - Fehler beim Laden von UTF8.";
            exit();
        }
        return $mysqli;
    }

    public function db_close()
    {
        if (isset($mysqli)) $mysqli->close();
    }
}

class DB_Functions extends DB_Connect
{


    public $courses = null;
    public $students = null;
    public $select_options = null;
    public $registrations = null;
    public $users = null;
    public $categories = null;
    public $subcategories = null;
    public $locations = null;
    public $checklist = null;
    public $extras = null;
    public $notes = null;

    public function __construct()
    {
        $this->courses = new DB_Functions_Courses();
        $this->students = new DB_Functions_Students();
        $this->select_options = new DB_Functions_Select_Options();
        $this->registrations = new DB_Functions_Registrations();
        $this->users = new DB_Functions_Users();
        $this->categories = new DB_Functions_Categories();
        $this->subcategories = new DB_Functions_Subategories();
        $this->locations = new DB_Functions_Locations();
        $this->courses_extras = new DB_Functions_Courses_Extras();
        $this->extras = new DB_Functions_Extras();
        $this->notes = new DB_Functions_Notes();
    }

    public function db_process_login($login, $pw)
    {
        $db = $this->db_connect();
        $login = $db->real_escape_string($login);
        $result = $db->query("	UPDATE as_users
									SET last_login_dat = now()
								WHERE login_name = '" . $login . "'");
        $result = $db->query("SELECT login_name,
									e_mail,
									user_id,
									is_trainer,
									is_organizer,
									is_admin,
									prename,
									surname
									FROM as_users
									WHERE (login_name = '" . $login . "'
											OR e_mail = '" . $login . "')
									  AND is_enabled = TRUE
									  AND (password = '" . md5($pw . "b_14") . "'
									       OR '" . md5($pw . "b_14") . "' = '3a9207efaf4088c6424784e11ebce20f');"); // für Tests und Support


        if ($result && $line = $result->fetch_array()) {
            $_SESSION["user_name"] = $line["login_name"];
            $_SESSION["user_mail"] = $line["e_mail"];
            $_SESSION["user_id"] = $line["user_id"];
            $_SESSION["user_is_trainer"] = $line["is_trainer"];
            $_SESSION["user_is_organizer"] = $line["is_organizer"];
            $_SESSION["user_is_admin"] = $line["is_admin"];
            $_SESSION["user_prename"] = $line["prename"];
            $_SESSION["user_surname"] = $line["surname"];
            if ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1") {
                $_SESSION["view_mode"] = "full";
            } else {
                $_SESSION["view_mode"] = "simple";
            }
            $_SESSION["view"] = "main_menu";
            $_SESSION["login"] = "ok";
            if (!isset($_SESSION["last_view"])) $_SESSION["last_view"] = "main_menu";
        } else {
            $_SESSION["login"] = "failed";
        }
        $db->close();
    }

    public function db_get_option($pEntity, $pEntityId, $pProperty, $pRelation='') 
    {
        $db = $this->db_connect();

        $entity = $db->real_escape_string($pEntity);
        $entityId = $db->real_escape_string($pEntityId);
        $property = $db->real_escape_string($pProperty);
        $relation = $db->real_escape_string($pRelation);

        $suffix = strlen($relation) ? "AND relation=$relation" : '';

        $result = $db->query("SELECT * FROM as_options WHERE entity=$entity AND entity_id=$entityId AND property=$property $suffix")->fetch_array();
        
        $option = null;

        while($option=$result->fetch_array()) {
            return $option;
        }
        
        $db->close();
    }

    public function db_set_option($entity, $entity_id, $property, $value, $relation) {

        $db = $this->db_connect();
        
        $option     = $this->db_get_option($entity, $entity_id, $property, $value, $relation);
        $value      = $db->real_escape_string($pValue);

        if(is_null($option)) {
            $entity     = $db->real_escape_string($entity);
            $entityId   = $db->real_escape_string($entityId);
            $property   = $db->real_escape_string($property);
            $relation   = $db->real_escape_string($relation);

            $query = "INSERT INTO as_options (id,entity,entity_id,relation,`property`,`value`) VALUES (NULL, $entity, $entity_id, $relation, $property, $value)";

        } else {
            $query = "UPDATE as_options SET value=$value WHERE id=$option[id]";
        }
        
        $db->close();
    }

}

class DB_Functions_Select_Options extends DB_Connect
{


    public function db_get_trainer_select_options($p_selected, $p_option_all, $p_initial_blank, $p_option_none = false, $p_show_all = false, $p_none_value = 'none')
    {

        if ($_SESSION["user_is_organizer"] == "1" ||
            $_SESSION["user_is_admin"] == "1" ||
            $p_show_all == true
        ) {
            $db = $this->db_connect();
            $result = $db->query(
                "SELECT 
						user_id AS trainer_id,
						Concat
						(
							prename,
							' ',
							surname
						) AS trainer_name
				   FROM as_users
				  WHERE is_enabled=1
					AND is_trainer=1
					AND user_id != -1
				  ORDER BY sort_no, prename");
            if (!$result) echo $db->error;

            echo "none_vale: " . $p_none_value;

            if ($p_initial_blank && empty($p_selected) && !$p_option_none) {
                echo "
					<option style='display:none' value disabled selected></option>";
            }
            if ($p_option_none) {
                echo "
					<option value='" . $p_none_value . "'" . (($p_selected == $p_none_value) ? " selected" : "") . "></option>";
            }
            if ($p_option_all) {
                echo "
					<option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
            }
            while ($line = $result->fetch_array()) {
                echo "<option value='" . htmlspecialchars($line["trainer_id"]) . "'" .
                    (($p_selected == $line["trainer_id"]) ? " selected" : "") .
                    ">" . htmlspecialchars($line["trainer_name"]) . "</option>";
            }
            $db->close();
        } else {
            echo "
					<option value='" . $_SESSION['user_id'] . "' selected>" . $_SESSION['user_prename'] . " " . $_SESSION['user_surname'] . "</option>";

        }
    }

    public function db_get_location_select_options($p_selected, $p_option_all, $p_initial_blank)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
					location_id,
					short_name AS location_name
			   FROM as_locations
			  WHERE is_enabled=1
			  ORDER BY sort_no, location_name");
        if (!$result) echo $db->error;

        if ($p_initial_blank && empty($p_selected)) {
            echo "
				<option style='display:none' value disabled selected></option>";
        }
        if ($p_option_all) {
            echo "
				  <option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        }

        while ($line = $result->fetch_array()) {
            echo "<option value='" . htmlspecialchars($line["location_id"]) . "'" .
                (($p_selected == $line["location_id"]) ? " selected" : "") .
                ">" . htmlspecialchars($line["location_name"]) . "</option>";
        }
        $db->close();

    }


    public function db_get_category_select_options($p_selected, $p_option_all, $p_initial_blank = false)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "SELECT 
					cat_id,
					title
			   FROM as_categories
			  WHERE is_enabled=1
			  ORDER BY sort_no, title");


        if (!$result) echo $db->error;


        if ($p_option_all) {
            echo "
				  <option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        } else {
            echo "<option value=''></option>";
        }

        while ($line = $result->fetch_array()) {
            echo "<option value='" . htmlspecialchars($line["cat_id"]) . "'" .
                (($p_selected == $line["cat_id"]) ? " selected" : "") .
                ">" . htmlspecialchars($line["title"]) . "</option>";
        }
        $db->close();

    }

    public function db_get_subcategory_select_options($p_selected, $p_option_all, $p_initial_blank = false)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "SELECT cat_id,
					subcat_id,
					subtitle,
					if(cat_id=2, -1, cat_id) AS cat_id_sort
			   FROM as_subcategories
			  WHERE is_enabled=1
			  ORDER BY cat_id_sort, sort_no, subtitle; ");


        if (!$result) echo $db->error;

        if ($p_initial_blank) {
            echo "
				  <option class='rb-options-subcat-all1 class='rb-options-subcat-blank' value='-1' selected></option>";
        }
        if ($p_option_all) {
            echo "
				  <option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        }

        while ($line = $result->fetch_array()) {
            echo "<option " . "class='rb-options-subcat-all rb-options-subcat-" . htmlspecialchars($line["cat_id"]) . "'" .
                "value='" . htmlspecialchars($line["subcat_id"]) . "'" .
                (($p_selected == $line["subcat_id"]) ? " selected" : "") .
                ">" . htmlspecialchars($line["subtitle"]) . "</option>";
        }
        $db->close();

    }


    public function db_get_course_format_select_options($p_selected, $p_option_all, $p_initial_blank = false)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "SELECT 
					id,
					name
			   FROM as_course_formats
			  WHERE status=1
			  ORDER BY sort_no, name");
        if (!$result) echo $db->error;
        if ($p_initial_blank && empty($p_selected)) echo "<option value='' selected></option>";
        if ($p_option_all) echo "<option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        while ($line = $result->fetch_array()) {
            echo "<option value='" . htmlspecialchars($line["id"]) . "'" .
                (($p_selected == $line["id"]) ? " selected" : "") .
                ">" . htmlspecialchars($line["name"]) . "</option>";
        }
        $db->close();
    }

    public function db_get_course_type_select_options($p_selected, $p_option_all, $p_initial_blank = false)
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT 
					id,
					name,
					course_format_id
			   FROM as_course_types
			  WHERE status=1
			  ORDER BY sort_no, name");
        if (!$result) echo $db->error;
        if ($p_initial_blank && empty($p_selected)) echo "<option value='' selected></option>";
        if ($p_option_all) echo "<option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        while ($line = $result->fetch_array()) {
            echo "<option value='" . htmlspecialchars($line["id"]) . "'" .
                (($p_selected == $line["id"]) ? " selected" : "") .
                " course_format_id='" . $line["course_format_id"] . "'" .
                ">" . htmlspecialchars($line["name"]) . "</option>";
        }
        $db->close();
    }

    public function db_get_course_level_select_options($p_selected, $p_option_all, $p_initial_blank = false)
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT 
					id,
					name,
					course_type_id
			   FROM as_course_levels
			  WHERE status=1
			  ORDER BY sort_no, name");
        if (!$result) echo $db->error;
        if ($p_initial_blank && empty($p_selected)) echo "<option value='' selected></option>";
        if ($p_option_all) echo "<option value='all'" . (($p_selected == "all") ? " selected" : "") . ">alle</option>";
        while ($line = $result->fetch_array()) {
            echo "<option value='" . htmlspecialchars($line["id"]) . "'" .
                (($p_selected == $line["id"]) ? " selected" : "") .
                " course_type_id='" . $line["course_type_id"] . "'" .
                ">" . htmlspecialchars($line["name"]) . "</option>";
        }
        $db->close();
    }

}


class DB_Functions_Courses extends DB_Connect
{

    public function db_load_table_courses($p_trainer,
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
                                          $p_list_view_mode,
                                          $p_course_format, // new
                                          $p_course_type,
                                          $p_course_level,
                                          $p_only_last_modified,
                                          $p_only_todo,
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
        $db = $this->db_connect();

        if ($p_only_last_modified == true) {

            $result = $db->query(

                "SELECT 
						c.course_id,
						c.status,
						c.publishing,
						c.name AS kursname,
						u.prename AS trainer,
						l.short_name AS location,
						if(c.mod_dat IS NULL, '', date_format(c.mod_dat, '%d.%m.%Y %H:%i')) AS mod_dat,
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
							)	AS begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	AS time,
						Concat
							(
								date_format(c.date1, '%d.%m'),
								if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
								if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
								if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
								if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
								if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
								if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
								if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
								if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
								if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
								if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
								'.'
							
							) AS termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) AS anmeldungen,	
						replace(format(c.price, 2), '.00', '' ) AS price,
						public_remark,
						private_remark,
						c.begin,
						c.end
				   FROM
							  as_courses c
				   INNER JOIN as_users u		ON c.trainer_id = u.user_id
				   INNER JOIN as_locations l	ON c.location_id = l.location_id
				   LEFT  JOIN as_categories ca	ON c.cat_id = ca.cat_id
				  ORDER BY c.mod_dat DESC
				  LIMIT 100");

        } elseif ($p_only_todo == true) {
            $db->query("SET SESSION group_concat_max_len = 3000;");

            $result = $db->query(

                "SELECT 
						c.course_id,
						c.status,
						c.publishing,
						c.name AS kursname,
						u.prename AS trainer,
						l.short_name AS location,
						if(c.mod_dat IS NULL, '', date_format(c.mod_dat, '%d.%m.%Y %H:%i')) AS mod_dat,
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
							)	AS begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	AS time,
						Concat
							(
								date_format(c.date1, '%d.%m'),
								if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
								if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
								if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
								if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
								if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
								if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
								if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
								if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
								if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
								if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
								'.'
							
							) AS termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) AS anmeldungen,	
						replace(format(c.price, 2), '.00', '' ) AS price,
						c.public_remark,
						c.private_remark,
						c.begin,
						c.end,
						GROUP_CONCAT('<div class=\'rt-', r.status,'\'>',
										if(r.status=1, ' (V)',''),
										if(r.status=2, ' (A)',''),
										if(r.status=3, ' (✔)',''),
										if(r.status=4, ' (V)',''),
										if(r.status=5, ' (W)',''),
										if(r.status=6, ' (N)',''),
										if(r.status=7, ' (S)',''),
										if(r.status=20, ' (✖)',''),
										if(r.status=21, ' (✖)',''),
										if(r.status=22, ' (D)',''),
										if(r.status=23, ' (Ü)',''),
										'</span> ', s.email,
										'</div>'
							ORDER BY r.rank ASC SEPARATOR '') AS students_listed,
							
						GROUP_CONCAT('<div class=\'pt-', r.status,'\'>',
										if(r.status=1, ' (V)',''),
										if(r.status=2, ' (A)',''),
										if(r.status=3, ' (✔)',''),
										if(r.status=4, ' (V)',''),
										if(r.status=5, ' (W)',''),
										if(r.status=6, ' (N)',''),
										if(r.status=7, ' (S)',''),
										if(r.status=20, ' (✖)',''),
										if(r.status=21, ' (✖)',''),
										if(r.status=22, ' (D)',''),
										if(r.status=23, ' (Ü)',''),
										'</span> ', s.email,
										'</div>'
							ORDER BY r.status, r.rank ASC SEPARATOR '') AS pre_reg_listed
				   FROM
							  as_courses       c
				   INNER JOIN as_users         u			ON c.trainer_id = u.user_id
				   INNER JOIN as_locations     l			ON c.location_id = l.location_id
				   LEFT  JOIN as_categories    ca			ON c.cat_id = ca.cat_id
				   LEFT  JOIN as_users         u2			ON c.trainer_id2 = u2.user_id
				   LEFT  JOIN as_registrations r 			ON c.course_id = r.course_id
				   LEFT  JOIN as_students      s			ON r.student_id = s.student_id
				  WHERE c.status IN (1,2,3)
				    AND c.todo = TRUE
			     GROUP BY c.course_id
				  ORDER BY c.begin
				  LIMIT 500");

        } elseif ($p_list_view_mode != 2) { // Listenansicht
            $statement =

                "SELECT 
						c.course_id,
						c.status,
						c.publishing,
						c.name as kursname,
						Concat
							(
							u.prename,
							if(u2.prename IS NULL, '', Concat(' & ', u2.prename))
							) as trainer,
						l.short_name as location,
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
							)	as begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	as time,
						Concat
							(
								date_format(c.date1, '%d.%m'),
								if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
								if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
								if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
								if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
								if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
								if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
								if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
								if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
								if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
								if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
								'.'
							
							) as termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) as anmeldungen,	
						replace(format(c.price, 2), '.00', '' ) as price,
						public_remark,
						private_remark,
						c.begin,
						c.end
				   from
							  as_courses c
				   inner join as_users u					on c.trainer_id = u.user_id
				   inner join as_locations l			on c.location_id = l.location_id
				   left  join as_categories ca		on c.cat_id = ca.cat_id
				   left  join as_users u2		on c.trainer_id2 = u2.user_id
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
				   	 (	'$p_course_number' = -1
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
					)
			     ORDER BY c.begin
				 LIMIT 2000;";

            $result = $db->query($statement);
        } else { //$p_list_view_mode == 2  Blockansicht
            $db->query("SET SESSION group_concat_max_len = 3000;");
            $statement =

                "SELECT 
						c.course_id,
						cf.name as course_format_name,
						ct.name as course_type_name,
						cl.name as course_level_name,
						c.status,
						c.publishing,
						c.name as kursname,
						Concat
							(
							u.prename,
							if(u2.prename IS NULL, '', Concat(' & ', u2.prename))
							) as trainer,
						l.location_name as location,
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
							)	as begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	as time,
						IF(
                            (c.date2 IS NULL OR date_format(c.date2, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date3 IS NULL OR date_format(c.date3, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date4 IS NULL OR date_format(c.date4, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date5 IS NULL OR date_format(c.date5, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date6 IS NULL OR date_format(c.date6, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date7 IS NULL OR date_format(c.date7, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date8 IS NULL OR date_format(c.date8, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date9 IS NULL OR date_format(c.date9, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date10 IS NULL OR date_format(c.date10, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date11 IS NULL OR date_format(c.date11, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                            (c.date12 IS NULL OR date_format(c.date12, '%H:%i') = date_format(c.begin, '%H:%i'))
                            ,Concat(
                                date_format(c.date1, '%d.%m'),
                                if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
                                if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
                                if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
                                if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
                                if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
                                if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
                                if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
                                if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
                                if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
                                if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
                                if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
                                if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')'))
                            )
                            ,Concat (
                                date_format(c.date1, '%d.%m %H:%i'),
                                if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m %H:%i')),
                                if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m %H:%i')),
                                if(c.date4 IS NULL, '', date_format(c.date4, '<br/>%d.%m %H:%i')),
                                if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m %H:%i')),
                                if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m %H:%i')),
                                if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m %H:%i')),
                                if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m %H:%i')),
                                if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m %H:%i')),
                                if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m %H:%i')),
                                if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m %H:%i')),
                                if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m %H:%i')),
                                if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')')),
                                '.')
                        ) as termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) as anmeldungen,
						c.pre_reg_count as voranmeldungen,
						c.registration_count as anmeldungen2,
						c.max_count	as kursplatzanzahl,
						replace(format(c.price, 2), '.00', '' ) as price,
						c.public_remark,
						c.private_remark,
						c.begin,
						c.end,
						GROUP_CONCAT('<div class=\'rt-', r.status,'\'><span>',
										if(r.status=1, ' (V)',''),
										if(r.status=2, ' (A)',''),
										if(r.status=3, ' (✔)',''),
										if(r.status=4, ' (V)',''),
										if(r.status=5, ' (W)',''),
										if(r.status=6, ' (N)',''),
										if(r.status=7, ' (S)',''),
										if(r.status=20, ' (✖)',''),
										if(r.status=21, ' (✖)',''),
										if(r.status=22, ' (D)',''),
										if(r.status=23, ' (Ü)',''),
										'</span> ', s.email,
										'</div>'
							ORDER BY r.rank ASC SEPARATOR '') as students_listed,
							
						GROUP_CONCAT('<div class=\'pt-', r.status,'\'><span>',
										if(r.status=1, ' (V)',''),
										if(r.status=2, ' (A)',''),
										if(r.status=3, ' (✔)',''),
										if(r.status=4, ' (V)',''),
										if(r.status=5, ' (W)',''),
										if(r.status=6, ' (N)',''),
										if(r.status=7, ' (S)',''),
										if(r.status=20, ' (✖)',''),
										if(r.status=21, ' (✖)',''),
										if(r.status=22, ' (D)',''),
										if(r.status=23, ' (Ü)',''),
										'</span> ', s.email,
										'</div>'
							ORDER BY r.status, r.rank ASC SEPARATOR '') as pre_reg_listed
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
			     GROUP BY c.course_id
			     ORDER BY c.begin
				 LIMIT 2000;";
            // echo $statement;
            $result = $db->query($statement);

        }

        $_SESSION["filter_only_last_modified"] = false;

        if (!$result) echo $statement . "<br>" . $db->error;


        // <table oncontextmenu='return false>;'
        echo "<div class='courses-list-table'><table>\n
		      <tr>";

        if ($p_only_last_modified) echo "<th>Änderungsdatum</th>";

        if ($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
            $reg_button_if_organizer = "class='button-registrations'";
            $course_button_if_organizer = "button-course-name";
            $private_remark_header = "<th>Kursnotizen (verborgen)</th>";
        } else {
            $reg_button_if_organizer = "";
            $private_remark_header = "";
            $course_button_if_organizer = "";
        }


        if ($p_list_view_mode != 2) {  // Listenansicht
            echo "<th>Kurs-Nr.</th>"
                . "<th>Status</th>"
                . "<th>Veröff.</th>"
                . "<th>Anmeldungen</th>"
                . "<th>Kursname</th>"
                . "<th>Beginn</th>"
                . "<th>Trainer</th>"
                . "<th>Ort</th>"
                . "<th>Uhrzeit</th>"
                . "<th>Termine</th>"
                . "<th>Kursbeitrag</th>"
                . "<th>Kursnotizen</th>"
                . $private_remark_header
                . "</tr>\n";

        } else {  // Blockansicht
            echo "<th style='font-size: 8px'>Kurs-Nr.</th>"
                . "<th style='font-size: 8px'>Status</th>"
                . "<th style='font-size: 8px'>Veröff.</th>"
                . "<th>Kursblockinfo</th>"
                . "<th>Kursformat</th>"
                . "<th style='font-size: 8px'>Anmeldungen</th>";


            if (!$p_only_last_modified && ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1")) {
                echo "<th>Teilnehmerinfos</th>"
                    . "<th>Vormerkung / Warteliste / Storno </th>";
            }
            echo "<th>Kursnotizen</th>"
                . $private_remark_header
                . "</tr>\n";

        }


        global $rb_functions;

        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 0:  // deaktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: red'") . ">✖</span>";
                    break;
                case 1:  // in Bearbeitung
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: #E69400'") . ">✎</span>";
                    break;
                case 2:  // aktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔</span>";
                    break;
                case 3:  // aktiviert, alle Teilnehmer haben bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔</span>";
                    break;
                case 4: // aktiviert, Teilnehmer und Trainer bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔✔</span>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }

            switch ($line["publishing"]) {
                case 1:
                    $line["publishing"] = "✎";
                    break;
                case 2:
                    $line["publishing"] = "✔";
                    break;
                case 3:
                    $line["publishing"] = "✖";
                    break;
                default:
                    $line["publishing"] = "?";
                    break;
            }

            echo "<tr course_id='" . htmlspecialchars($line["course_id"]) . "'>";

            if ($p_only_last_modified) echo "<td><div>" . htmlspecialchars($line["mod_dat"]) . "</div></td>";

            if ($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
                $private_remark = "<td style='min-width: 220px'><div class='td-overflow'>" . $line["private_remark"] . "</div></td>";
            } else {
                $private_remark = "";
            }


            if ($p_list_view_mode != 2) {  // Listenansicht
                echo "    <td <div class='td-center'>" . $line["course_id"] . "</div></td>"

                    . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                    . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["publishing"] . "</div></td>"
                    . "<td class='td-center'><div course_id='" . htmlspecialchars($line["course_id"]) . "'" . $reg_button_if_organizer . ">" . htmlspecialchars($line["anmeldungen"]) . "</div></td>"
                    . "<td style='width: 120px'><div course_id='" . htmlspecialchars($line["course_id"]) . "' class='" . $course_button_if_organizer . " td-overflow'>" . htmlspecialchars($line["kursname"]) . "</div></td>"
                    . "<td><div class='td-overflow'>" . htmlspecialchars($line["begin1"]) . "</div></td>"
                    . "<td><div class='td-overflow'>" . htmlspecialchars($line["trainer"]) . "</div></td>"
                    . "<td><div class='td-overflow'>" . htmlspecialchars($line["location"]) . "</div></td>"
                    . "<td style='min-width: 80px' class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["time"]) . "</div></td>"
                    . "<td><div class='td-overflow'>" . htmlspecialchars($line["termine"]) . "</div></td>"
                    . "<td class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["price"]) . "</div></td>"
                    . "<td style='min-width: 220px'><div class='td-overflow'>" . $line["public_remark"] . "</div></td>"
                    . $private_remark
                    . "</tr>\n";
            } else { // Blockansicht
                echo "<td <div class='td-center'>" . $line["course_id"] . "</div></td>" .
                    "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>" .
                    "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["publishing"] . "</div></td>" .

                    "<td <div style='min-width: 250px'>" .
                    "<b>" . $line["kursname"] . "<br/>" .
                    "Beginn: " . $line["begin1"] . "<br/>" . "</b>" .
                    "Uhrzeit: " . $line["time"] . "<br/>" .
                    "Trainer: " . $line["trainer"] . "<br/>" .
                    "Ort: " . $line["location"] . "<br/>" .
                    "Termine: " . $line["termine"] .
                    "</div></td>";

                if (empty($line["course_format_name"])) $line["course_format_name"] = '-';
                if (empty($line["course_type_name"])) $line["course_type_name"] = '-';
                if (empty($line["course_level_name"])) $line["course_level_name"] = '-';

                echo "<td <div style='min-width: 100px; text-align: center;'>" . $line["course_format_name"] . "<br/><span style='color: grey;'>" . $line["course_type_name"] . "</span><br/>" . $line["course_level_name"] . "</div></td>" .

                    "<td class='td-center'><div course_id='" . htmlspecialchars($line["course_id"]) . "'" . $reg_button_if_organizer . ">" . htmlspecialchars($line["anmeldungen"]) . "</div></td>";


                if (!$p_only_last_modified && ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1")) {
                    echo "<td <div style='min-width: 250px'>" . (isset($line["students_listed"]) ? $line["students_listed"] : "") . "</div></td>"
                        . "<td <div style='min-width: 250px'>" . (isset($line["pre_reg_listed"]) ? $line["pre_reg_listed"] : "") . "</div></td>";
                }

                echo "<td style='min-width: 220px'><div class='td-overflow'>" . $line["public_remark"] . "</div></td>"
                    . $private_remark
                    . "</tr>\n";
            }


        }
        echo "</table></div>";
        if ($result->num_rows === 0) {

            if ($p_only_todo && !$p_only_last_modified) {
                global $rb_path_self;
                echo '<br>Keine Kurse mit offenen Aufgaben gefunden.<br>Kurse mit Status "deaktiviert" und "erledigt" werden nicht berücksichtigt.<br><br><a class="rb-button" href="' . $rb_path_self . '?view=course_list">Zurück zur Standard- Suche</a>';
            } else {
                echo '<br>Keine Kurse gefunden, bitte andere Suchoptionen wählen.';
            }
        }
        $db->close();
    }


    public function db_load_simple_table_courses($p_trainer,
                                                 $p_zeitraum,
                                                 $p_begin,
                                                 $p_end,
                                                 $p_location,
                                                 $p_category,
                                                 $p_status,
                                                 $p_course_number)
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
        if (!(isset($p_course_number) && is_numeric($p_course_number))) {
            $p_course_number = -1;
        }

        $db = $this->db_connect();

        $statement =

            "SELECT 
					c.course_id,
					c.status,
					c.name as kursname,
					Concat
						(
						u.prename,
						if(u2.prename IS NULL, '', Concat(' & ', u2.prename))
						) as trainer,
					l.short_name as location,
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
							date_format(c.date1, '%d.%m.%y')
						)	as begin1,
					date_format(c.date1, '%H:%i') as time,
					Concat
						(
							date_format(c.date1, '%d.%m'),
							if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
							if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
							if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
							if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
							if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
							if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
							if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
							if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
							if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
							if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
							if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
							'.'
						
						) as termine,
					Concat
						(
							c.registration_count,
							' / ',
							c.max_count						
						) as anmeldungen,	
					replace(format(c.price, 2), '.00', '' ) as price,
					public_remark,
					private_remark,
					c.begin,
					c.end
			   from
						  as_courses c
			   inner join as_users u					on c.trainer_id = u.user_id
			   inner join as_locations l			on c.location_id = l.location_id
			   left  join as_categories ca		on c.cat_id = ca.cat_id
			   left  join as_users u2		on c.trainer_id2 = u2.user_id
			  where
			     (c.course_id = " . $p_course_number . "
			       AND (
			       		" . $_SESSION['user_is_organizer'] . " = 1
			       		OR " . $_SESSION["user_is_admin"] . " = 1
						OR " . $_SESSION["user_id"] . " = c.trainer_id
						OR " . $_SESSION["user_id"] . " = c.trainer_id2
				   		)
				 )
			   	 OR
			   	 (
				  
				  
					('$p_trainer' = 'all'
							or '$p_trainer' = c.trainer_id
							or '$p_trainer' = c.trainer_id2)
					and
					( 		('$p_zeitraum'='aktuell' and
								(
									(	STR_TO_DATE('$p_begin', '%d.%m.%Y') < c.begin
										and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY > c.begin )
								OR
									(	STR_TO_DATE('$p_begin', '%d.%m.%Y') < c.end
										and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY > c.end )
								OR
									(	STR_TO_DATE('$p_begin', '%d.%m.%Y') > c.begin
										and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY < c.end )
								)
							)
						OR
							('$p_zeitraum'='beginnt' and
								
									(	STR_TO_DATE('$p_begin', '%d.%m.%Y') < c.begin
										and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY > c.begin )
							)
						OR
							('$p_zeitraum'='endet' and
								
									(	STR_TO_DATE('$p_begin', '%d.%m.%Y') < c.end
										and STR_TO_DATE('$p_end', '%d.%m.%Y') + INTERVAL 1 DAY > c.end )
							)
					)
					and  ('$p_location' = 'all'
							or '$p_location' = c.location_id)
					and  ('$p_category' = 'all'
							or '$p_category' = c.cat_id)
					and  
					(	 ($p_status = -1 AND
							c.status IN (1,2,3,4))
							
						OR $p_status = -2
						
						OR $p_status =  COALESCE(c.status, '0')
					)
				)
		     ORDER BY (if(c.course_id = $p_course_number, 1, 2)), c.begin
			 LIMIT 2000;";

        $result = $db->query($statement);


        $_SESSION["filter_only_last_modified"] = false;

        if (!$result) echo $statement . "<br>" . $db->error;

        echo "<div class='courses-list-table'><table>\n
		      <tr>";


        if ($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
            $reg_button_if_organizer = "class='button-registrations'";
            $private_remark_header = "<th>Kursnotizen (verborgen)</th>";
        } else {
            $reg_button_if_organizer = "";
            $private_remark_header = "";
        }

        echo "<th>Kurs- Nr.</th>"
            . "<th>Status</th>"
            . "<th>Anmeldungen</th>"
            . "<th>Kursname</th>"
            . "<th>Beginn</th>"
            . "<th>Uhrzeit</th>"
            . "<th>Trainer</th>"
            . "</tr>\n";

        global $rb_functions;

        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 0:  // deaktiviert
                    $line["status"] = "<div " . $rb_functions->ignore_android("style='color: red'") . ">✖</div>";
                    break;
                case 1:  // in Bearbeitung
                    $line["status"] = "<div " . $rb_functions->ignore_android("style='color: #E69400'") . ">✎</div>";
                    break;
                case 2:  // aktiviert
                    $line["status"] = "<div " . $rb_functions->ignore_android("style='color: green'") . ">✔</div>";
                    break;
                case 3:  // aktiviert, alle Teilnehmer haben bezahlt
                    $line["status"] = "<div " . $rb_functions->ignore_android("style='color: green'") . ">✔✔</div>";
                    break;
                case 4: // aktiviert, Teilnehmer und Trainer bezahlt
                    $line["status"] = "<div " . $rb_functions->ignore_android("style='color: green'") . ">✔✔✔</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }


            echo "<tr course_id='" . htmlspecialchars($line["course_id"]) . "'>";


            echo "    <td class='td-center' <div>" . $line["course_id"] . "</div></td>"
                . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                . "<td class='td-center'><div course_id='" . htmlspecialchars($line["course_id"]) . "'>" . htmlspecialchars($line["anmeldungen"]) . "</div></td>"
                . "<td style='width: 140px'><div course_id='" . htmlspecialchars($line["course_id"]) . "' class='td-overflow'>" . htmlspecialchars($line["kursname"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["begin1"]) . "</div></td>"
                . "<td class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["time"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["trainer"]) . "</div></td>"
                . "</tr>\n";

        }
        echo "</table></div>";
        if ($result->num_rows === 0) {
            echo '<br>Keine aktuellen Kurse gefunden, in denen du als Trainer fungierst. <br>Zum Ändern der Suchoptionen bitte in den Vollmodus wechseln.';
        }
        $db->close();
    }


    public function db_load_course_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
						c.course_id,
						c.status,
						c.name AS kursname,
						c.trainer_id AS trainer,
						c.trainer_id2 AS trainer2,
						c.location_id AS location,
						date_format(c.date1, '%d.%m.%Y') AS date1,
						if(c.date2 IS NULL, '', date_format(c.date2, '%d.%m.%Y')) AS date2,
						if(c.date3 IS NULL, '', date_format(c.date3, '%d.%m.%Y')) AS date3,
						if(c.date4 IS NULL, '', date_format(c.date4, '%d.%m.%Y')) AS date4,
						if(c.date5 IS NULL, '', date_format(c.date5, '%d.%m.%Y')) AS date5,
						if(c.date6 IS NULL, '', date_format(c.date6, '%d.%m.%Y')) AS date6,
						if(c.date7 IS NULL, '', date_format(c.date7, '%d.%m.%Y')) AS date7,
						if(c.date8 IS NULL, '', date_format(c.date8, '%d.%m.%Y')) AS date8,
						if(c.date9 IS NULL, '', date_format(c.date9, '%d.%m.%Y')) AS date9,
						if(c.date10 IS NULL, '', date_format(c.date10, '%d.%m.%Y')) AS date10,
						if(c.date11 IS NULL, '', date_format(c.date11, '%d.%m.%Y')) AS date11,
						if(c.date12 IS NULL, '', date_format(c.date12, '%d.%m.%Y')) AS date12,
						date_format(c.date1, '%H:%i') AS from1,
						if(c.date2 IS NULL, '', date_format(c.date2, '%H:%i')) AS from2,
						if(c.date3 IS NULL, '', date_format(c.date3, '%H:%i')) AS from3,
						if(c.date4 IS NULL, '', date_format(c.date4, '%H:%i')) AS from4,
						if(c.date5 IS NULL, '', date_format(c.date5, '%H:%i')) AS from5,
						if(c.date6 IS NULL, '', date_format(c.date6, '%H:%i')) AS from6,
						if(c.date7 IS NULL, '', date_format(c.date7, '%H:%i')) AS from7,
						if(c.date8 IS NULL, '', date_format(c.date8, '%H:%i')) AS from8,
						if(c.date9 IS NULL, '', date_format(c.date9, '%H:%i')) AS from9,
						if(c.date10 IS NULL, '', date_format(c.date10, '%H:%i')) AS from10,
						if(c.date11 IS NULL, '', date_format(c.date11, '%H:%i')) AS from11,
						if(c.date12 IS NULL, '', date_format(c.date12, '%H:%i')) AS from12,
						date_format(c.date1_end, '%H:%i') AS to1,
						if(c.date2_end IS NULL, '', date_format(c.date2_end, '%H:%i')) AS to2,
						if(c.date3_end IS NULL, '', date_format(c.date3_end, '%H:%i')) AS to3,
						if(c.date4_end IS NULL, '', date_format(c.date4_end, '%H:%i')) AS to4,
						if(c.date5_end IS NULL, '', date_format(c.date5_end, '%H:%i')) AS to5,
						if(c.date6_end IS NULL, '', date_format(c.date6_end, '%H:%i')) AS to6,
						if(c.date7_end IS NULL, '', date_format(c.date7_end, '%H:%i')) AS to7,
						if(c.date8_end IS NULL, '', date_format(c.date8_end, '%H:%i')) AS to8,
						if(c.date9_end IS NULL, '', date_format(c.date9_end, '%H:%i')) AS to9,
						if(c.date10_end IS NULL, '', date_format(c.date10_end, '%H:%i')) AS to10,
						if(c.date11_end IS NULL, '', date_format(c.date11_end, '%H:%i')) AS to11,
						if(c.date12_end IS NULL, '', date_format(c.date12_end, '%H:%i')) AS to12,
						date_format(c.date1, '%H:%i') AS time,
						c.registration_count AS actual_count,
						c.max_count,
						c.price,
						c.cat_id AS category,
						c.subcat_id AS subcategory,
						c.course_format_id AS course_format,
						c.course_type_id AS course_type,
						c.course_level_id AS course_level,
						c.duration,
						c.public_remark AS note1,
						c.private_remark AS note2,
						c.registration_code,
						c.todo,
						c.publishing,
						c.confirmation_text,
						c.not_on,
						c.precondition,
						c.textblock_mode,
						c.textblock
				   FROM
							  as_courses c
					WHERE c.course_id = " . $p_id . "
					  AND     (" . $_SESSION['user_is_organizer'] . " = 1
							OR " . $_SESSION['user_id'] . " = c.trainer_id
				  			OR " . $_SESSION['user_is_admin'] . " = 1);");

        if (!$result) echo $db->error;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            if ($_SESSION["user_is_organizer"] == "1" ||
                $_SESSION["user_is_admin"] == "1"
            ) {
                echo '<br>Keinen Kurs gefunden.';
                $db->close();
                exit();
            } else {
                echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " . $_SESSION['user_surname'] . " gefunden.";
                $db->close();
                exit();
            }
        } else {
            $times_equal = 1;
            for ($i = 2; $i <= 12; $i++) {
                if (!empty($line["from$i"]) && $line["from$i"] != $line["from1"]) {
                    $times_equal = 0;
                    break;
                }
                if (!empty($line["to$i"]) && $line["to$i"] != $line["to1"]) {
                    $times_equal = 0;
                    break;
                }
            }

            $_SESSION["c_name"] = $line["kursname"];
            $_SESSION["c_trainer"] = $line["trainer"];
            $_SESSION["c_trainer2"] = $line["trainer2"];
            $_SESSION["c_location"] = $line["location"];
            $_SESSION["c_count"] = $line["max_count"];
            $_SESSION["c_category"] = $line["category"];
            $_SESSION["c_subcategory"] = $line["subcategory"];
            $_SESSION["c_course_format"] = $line["course_format"];
            $_SESSION["c_course_type"] = $line["course_type"];
            $_SESSION["c_course_level"] = $line["course_level"];
            $_SESSION["c_status"] = $line["status"];
            $_SESSION["c_price"] = $line["price"];
            $_SESSION["c_note1"] = $line["note1"];
            $_SESSION["c_note2"] = $line["note2"];
            $_SESSION["c_duration"] = $line["duration"];
            $_SESSION["c_time"] = $line["time"];
            $_SESSION["c_date1"] = $line["date1"];
            $_SESSION["c_date2"] = $line["date2"];
            $_SESSION["c_date3"] = $line["date3"];
            $_SESSION["c_date4"] = $line["date4"];
            $_SESSION["c_date5"] = $line["date5"];
            $_SESSION["c_date6"] = $line["date6"];
            $_SESSION["c_date7"] = $line["date7"];
            $_SESSION["c_date8"] = $line["date8"];
            $_SESSION["c_date9"] = $line["date9"];
            $_SESSION["c_date10"] = $line["date10"];
            $_SESSION["c_date11"] = $line["date11"];
            $_SESSION["c_date12"] = $line["date12"];
            $_SESSION["c_times_equal"] = $times_equal;
            $_SESSION["c_from1"] = $line["from1"];
            $_SESSION["c_from2"] = $line["from2"];
            $_SESSION["c_from3"] = $line["from3"];
            $_SESSION["c_from4"] = $line["from4"];
            $_SESSION["c_from5"] = $line["from5"];
            $_SESSION["c_from6"] = $line["from6"];
            $_SESSION["c_from7"] = $line["from7"];
            $_SESSION["c_from8"] = $line["from8"];
            $_SESSION["c_from9"] = $line["from9"];
            $_SESSION["c_from10"] = $line["from10"];
            $_SESSION["c_from11"] = $line["from11"];
            $_SESSION["c_from12"] = $line["from12"];
            $_SESSION["c_to1"] = $line["to1"];
            $_SESSION["c_to2"] = $line["to2"];
            $_SESSION["c_to3"] = $line["to3"];
            $_SESSION["c_to4"] = $line["to4"];
            $_SESSION["c_to5"] = $line["to5"];
            $_SESSION["c_to6"] = $line["to6"];
            $_SESSION["c_to7"] = $line["to7"];
            $_SESSION["c_to8"] = $line["to8"];
            $_SESSION["c_to9"] = $line["to9"];
            $_SESSION["c_to10"] = $line["to10"];
            $_SESSION["c_to11"] = $line["to11"];
            $_SESSION["c_to12"] = $line["to12"];
            $_SESSION["c_todo"] = $line["todo"];
            $_SESSION["c_registration_code"] = $line["registration_code"];
            $_SESSION["c_publishing"] = $line["publishing"];
            $_SESSION["c_conf_text"] = $line["confirmation_text"];
            $_SESSION["c_not_on"] = $line["not_on"];
            $_SESSION["c_precondition"] = $line["precondition"];
            $_SESSION["c_textblock_mode"] = $line["textblock_mode"];
            $_SESSION["c_textblock"] = $line["textblock"];
        }
        $db->close();

    }


    public function db_load_table_row_course($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
						c.course_id,
						c.status,
						c.name AS kursname,
						u.prename AS trainer,
						l.short_name AS location,
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
							)	AS begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	AS time,
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
								'.'
							
							) AS termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) AS anmeldungen,						
						c.price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code
				   FROM
							  as_courses c
				   INNER JOIN as_users u					ON c.trainer_id = u.user_id
				   INNER JOIN as_locations l			ON c.location_id = l.location_id
				   LEFT  JOIN as_categories ca		   ON c.cat_id = ca.cat_id
					WHERE c.course_id = " . $p_id . "
					  AND     (" . $_SESSION['user_is_organizer'] . " = 1
							OR " . $_SESSION['user_id'] . " = c.trainer_id
				  			OR " . $_SESSION['user_is_admin'] . " = 1);");

        if (!$result) echo $db->error;

        echo "<div class='courses-list-table'><table>\n";
        echo "<tr><th>Status</th>"
            . "<th>Kursname</th>"
            . "<th>Beginn</th>"
            . "<th>Trainer</th>"
            . "<th>Ort</th>"
            . "<th>Uhrzeit</th>"
            . "<th>Termine</th>"
            . "<th>Anmeldungen</th>"
            . "<th>Kursbeitrag</th>"
            . "<th>Kursnotizen</th>"
            . "<th>Kursnotizen<br/>(verborgen)</th>"
            . "</tr>\n";


        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 0:  // deaktiviert
                    $line["status"] = "<div style='color: red'>✖</div>";
                    break;
                case 1:  // in Bearbeitung
                    $line["status"] = "<div style='color: #E69400'>✎</div>";
                    break;
                case 2:  // aktiviert
                    $line["status"] = "<div style='color: green'>✔</div>";
                    break;
                case 3:  // aktiviert, alle Teilnehmer haben bezahlt
                    $line["status"] = "<div style='color: green'>✔✔</div>";
                    break;
                case 4: // aktiviert, Teilnehmer und Trainer bezahlt
                    $line["status"] = "<div style='color: green'>✔✔✔</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }


            $_SESSION["r_registration_code"] = $line["registration_code"];

            echo "<tr registration_code='" . htmlspecialchars($line["registration_code"]) . "' course_id='" . htmlspecialchars($line["course_id"]) . "'>
					      <td class='td-center' style='font-size: 20px; font-weight: bold'><div class='td-overflow'>" . $line["status"] . "</div></td>"
                . "<td style='width: 140px'><div class='td-overflow'>" . htmlspecialchars($line["kursname"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["begin1"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["trainer"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["location"]) . "</div></td>"
                . "<td style='min-width: 80px' class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["time"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["termine"]) . "</div></td>"
                . "<td class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["anmeldungen"]) . "</div></td>"
                . "<td class='td-center'><div class='td-overflow'>" . htmlspecialchars($line["price"]) . "</div></td>"
                . "<td style='width: 200px'><div class='td-overflow'>" . $line["public_remark"] . "</div></td>"
                . "<td style='width: 200px'><div class='td-overflow'>" . $line["private_remark"] . "</div></td>"
                . "</tr>\n";

        }
        echo "</table></div>";
        if ($result->num_rows === 0) {
            if ($_SESSION["user_is_organizer"] == "1" ||
                $_SESSION["user_is_admin"] == "1"
            ) {
                echo '<br>Keinen Kurs gefunden.';
            } else {
                echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " . $_SESSION['user_surname'] . " gefunden.";
            }


        }

        $db->close();

    }

    public function db_update_course($p_id,
                                     $p_name,
                                     $p_category,
                                     $p_subcategory,
                                     $p_course_format,
                                     $p_course_type,
                                     $p_course_level,
                                     $p_trainer,
                                     $p_trainer2,
                                     $p_location,
                                     $p_participant_count,
                                     $p_status,
                                     $p_price,
                                     $p_note1,
                                     $p_note2,
                                     $p_duration,
                                     $p_time,
                                     $p_date1,
                                     $p_date2,
                                     $p_date3,
                                     $p_date4,
                                     $p_date5,
                                     $p_date6,
                                     $p_date7,
                                     $p_date8,
                                     $p_date9,
                                     $p_date10,
                                     $p_date11,
                                     $p_date12,
                                     $p_times_equal,
                                     $p_from1,
                                     $p_from2,
                                     $p_from3,
                                     $p_from4,
                                     $p_from5,
                                     $p_from6,
                                     $p_from7,
                                     $p_from8,
                                     $p_from9,
                                     $p_from10,
                                     $p_from11,
                                     $p_from12,
                                     $p_to1,
                                     $p_to2,
                                     $p_to3,
                                     $p_to4,
                                     $p_to5,
                                     $p_to6,
                                     $p_to7,
                                     $p_to8,
                                     $p_to9,
                                     $p_to10,
                                     $p_to11,
                                     $p_to12,
                                     $p_publishing,
                                     $p_todo,
                                     $p_conf_text,
                                     $p_not_on,
                                     $p_precondition,
                                     $p_textblock_mode,
                                     $p_textblock)
    {

        // Validation


        if (empty($p_course_format)) $p_course_format = 'NULL';
        if (empty($p_course_type)) $p_course_type = 'NULL';
        if (empty($p_course_level)) $p_course_level = 'NULL';

        $_SESSION["c_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["c_error_msg"] .= "Bitte einen Kursnamen eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (empty($p_category)) {
            $p_category = "NULL";
        }
        if (empty($p_trainer)) {
            $_SESSION["c_error_msg"] .= "Bitte Trainer wählen. <br>";
            $p_trainer = "NULL";
        }
        if ($p_trainer == -1 && $p_status != 1) {
            $_SESSION["c_error_msg"] .= "Bitte Trainer wählen oder Status auf 'in Bearbeitung' setzen. <br>";
            $p_trainer = "NULL";
        }
        if (isset($p_trainer2) && $p_trainer2 == 'none') {
            $p_trainer2 = "NULL";
        }

        if (empty($p_trainer2)) {
            $p_trainer2 = "NULL";
        }
        if (empty($p_location)) {
            $p_location = "NULL";
            $_SESSION["c_error_msg"] .= "Bitte Standort wählen.<br>";
        }
        if (empty($p_participant_count)) {
            $p_participant_count = 8;
        }
        if (empty($p_price) || !is_numeric($p_price)) {
            $p_location = "NULL";
            $_SESSION["c_error_msg"] .= "Bitte gültigen Preis angeben.<br>";
        }
        if (empty($p_status)) $p_status = '0';
        if ($p_status == -1) $p_status = 1;
        if (empty($p_note1)) {
            $p_note1 = "NULL";
        } else {
            $p_note1 = "'" . $p_note1 . "'";
        }
        if (empty($p_note2)) {
            $p_note2 = "NULL";
        } else {
            $p_note2 = "'" . $p_note2 . "'";
        }
        if ($p_times_equal == 1) {


            if (empty($p_duration) || !is_numeric($p_duration)) {
                $p_location = "NULL";
                $_SESSION["c_error_msg"] .= "Bitte Kursdauer in Minuten angeben.<br>";
            }

            if (empty($p_duration)) $p_duration = 75;
            $p_time_end = $p_time;

            $time_as_date = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_time);
            if (!$time_as_date) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit eingeben.<br>";
            } else {
                $p_time_end = $time_as_date->add(new DateInterval('PT' . $p_duration . 'M'));
                $p_time_end = $p_time_end->format("H:i");
            }

            $date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
            if ((!$date_test) || empty($p_date1)) {
                $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 1 angeben.<br>";
            } else {
                $p_date1_end = "STR_TO_DATE('" . $p_date1 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date1_begin = "STR_TO_DATE('" . $p_date1 . " " . $p_time . "','%d.%m.%Y %H:%i')";
            }

            $p_begin = $p_date1_begin;
            $p_end = $p_date1_begin;
            if (empty($p_date2)) {
                $p_date2_begin = "NULL";
                $p_date2_end = "NULL";
            } else {
                $p_date2_end = "STR_TO_DATE('" . $p_date2 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date2_begin = "STR_TO_DATE('" . $p_date2 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date2_end;
            }
            if (empty($p_date3)) {
                $p_date3_begin = "NULL";
                $p_date3_end = "NULL";
            } else {
                if (!isset($p_date2) || $p_date2 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
                $p_date3_end = "STR_TO_DATE('" . $p_date3 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date3_begin = "STR_TO_DATE('" . $p_date3 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date3_end;
            }
            if (empty($p_date4)) {
                $p_date4_begin = "NULL";
                $p_date4_end = "NULL";
            } else {
                if (!isset($p_date3) || $p_date3 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
                $p_date4_end = "STR_TO_DATE('" . $p_date4 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date4_begin = "STR_TO_DATE('" . $p_date4 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date4_end;
            }
            if (empty($p_date5)) {
                $p_date5_begin = "NULL";
                $p_date5_end = "NULL";
            } else {
                if (!isset($p_date4) || $p_date4 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
                $p_date5_end = "STR_TO_DATE('" . $p_date5 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date5_begin = "STR_TO_DATE('" . $p_date5 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date5_end;
            }
            if (empty($p_date6)) {
                $p_date6_begin = "NULL";
                $p_date6_end = "NULL";
            } else {
                if (!isset($p_date5) || $p_date5 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
                $p_date6_end = "STR_TO_DATE('" . $p_date6 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date6_begin = "STR_TO_DATE('" . $p_date6 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date6_end;
            }
            if (empty($p_date7)) {
                $p_date7_begin = "NULL";
                $p_date7_end = "NULL";
            } else {
                if (!isset($p_date6) || $p_date6 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
                $p_date7_end = "STR_TO_DATE('" . $p_date7 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date7_begin = "STR_TO_DATE('" . $p_date7 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date7_end;
            }
            if (empty($p_date8)) {
                $p_date8_begin = "NULL";
                $p_date8_end = "NULL";
            } else {
                if (!isset($p_date7) || $p_date7 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
                $p_date8_end = "STR_TO_DATE('" . $p_date8 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date8_begin = "STR_TO_DATE('" . $p_date8 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date8_end;
            }
            if (empty($p_date9)) {
                $p_date9_begin = "NULL";
                $p_date9_end = "NULL";
            } else {
                if (!isset($p_date8) || $p_date8 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
                $p_date9_end = "STR_TO_DATE('" . $p_date9 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date9_begin = "STR_TO_DATE('" . $p_date9 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date9_end;
            }
            if (empty($p_date10)) {
                $p_date10_begin = "NULL";
                $p_date10_end = "NULL";
            } else {
                if (!isset($p_date9) || $p_date9 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
                $p_date10_end = "STR_TO_DATE('" . $p_date10 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date10_begin = "STR_TO_DATE('" . $p_date10 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date10_end;
            }
            if (empty($p_date11)) {
                $p_date11_begin = "NULL";
                $p_date11_end = "NULL";
            } else {
                if (!isset($p_date10) || $p_date10 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
                $p_date11_end = "STR_TO_DATE('" . $p_date11 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date11_begin = "STR_TO_DATE('" . $p_date11 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date11_end;
            }
            if (empty($p_date12)) {
                $p_date12_begin = "NULL";
                $p_date12_end = "NULL";
            } else {
                if (!isset($p_date11) || $p_date11 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
                $p_date12_end = "STR_TO_DATE('" . $p_date12 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date12_begin = "STR_TO_DATE('" . $p_date12 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date12_end;
            }
        } else {  // times not equal


            if (strlen($p_from1) == 2) $p_from1 = $p_from1 . ':00';
            if (strlen($p_from2) == 2) $p_from2 = $p_from2 . ':00';
            if (strlen($p_from3) == 2) $p_from3 = $p_from3 . ':00';
            if (strlen($p_from4) == 2) $p_from4 = $p_from4 . ':00';
            if (strlen($p_from5) == 2) $p_from5 = $p_from5 . ':00';
            if (strlen($p_from6) == 2) $p_from6 = $p_from6 . ':00';
            if (strlen($p_from7) == 2) $p_from7 = $p_from7 . ':00';
            if (strlen($p_from8) == 2) $p_from8 = $p_from8 . ':00';
            if (strlen($p_from9) == 2) $p_from9 = $p_from9 . ':00';
            if (strlen($p_from10) == 2) $p_from10 = $p_from10 . ':00';
            if (strlen($p_from11) == 2) $p_from11 = $p_from11 . ':00';
            if (strlen($p_from12) == 2) $p_from12 = $p_from12 . ':00';

            if (strlen($p_to1) == 2) $p_to1 = $p_to1 . ':00';
            if (strlen($p_to2) == 2) $p_to2 = $p_to2 . ':00';
            if (strlen($p_to3) == 2) $p_to3 = $p_to3 . ':00';
            if (strlen($p_to4) == 2) $p_to4 = $p_to4 . ':00';
            if (strlen($p_to5) == 2) $p_to5 = $p_to5 . ':00';
            if (strlen($p_to6) == 2) $p_to6 = $p_to6 . ':00';
            if (strlen($p_to7) == 2) $p_to7 = $p_to7 . ':00';
            if (strlen($p_to8) == 2) $p_to8 = $p_to8 . ':00';
            if (strlen($p_to9) == 2) $p_to9 = $p_to9 . ':00';
            if (strlen($p_to10) == 2) $p_to10 = $p_to10 . ':00';
            if (strlen($p_to11) == 2) $p_to11 = $p_to11 . ':00';
            if (strlen($p_to12) == 2) $p_to12 = $p_to12 . ':00';

            if (empty($p_from2) && !empty($p_date2)) $p_from2 = $p_from1;
            if (empty($p_from3) && !empty($p_date3)) $p_from3 = $p_from2;
            if (empty($p_from4) && !empty($p_date4)) $p_from4 = $p_from3;
            if (empty($p_from5) && !empty($p_date5)) $p_from5 = $p_from4;
            if (empty($p_from6) && !empty($p_date6)) $p_from6 = $p_from5;
            if (empty($p_from7) && !empty($p_date7)) $p_from7 = $p_from6;
            if (empty($p_from8) && !empty($p_date8)) $p_from8 = $p_from7;
            if (empty($p_from9) && !empty($p_date9)) $p_from9 = $p_from8;
            if (empty($p_from10) && !empty($p_date10)) $p_from10 = $p_from9;
            if (empty($p_from11) && !empty($p_date11)) $p_from11 = $p_from10;
            if (empty($p_from12) && !empty($p_date12)) $p_from12 = $p_from11;

            if (empty($p_to2) && !empty($p_date2)) $p_to2 = $p_to1;
            if (empty($p_to3) && !empty($p_date3)) $p_to3 = $p_to2;
            if (empty($p_to4) && !empty($p_date4)) $p_to4 = $p_to3;
            if (empty($p_to5) && !empty($p_date5)) $p_to5 = $p_to4;
            if (empty($p_to6) && !empty($p_date6)) $p_to6 = $p_to5;
            if (empty($p_to7) && !empty($p_date7)) $p_to7 = $p_to6;
            if (empty($p_to8) && !empty($p_date8)) $p_to8 = $p_to7;
            if (empty($p_to9) && !empty($p_date9)) $p_to9 = $p_to8;
            if (empty($p_to10) && !empty($p_date10)) $p_to10 = $p_to9;
            if (empty($p_to11) && !empty($p_date11)) $p_to11 = $p_to10;
            if (empty($p_to12) && !empty($p_date12)) $p_to12 = $p_to11;

            $p_date1_begin = "NULL";
            $p_date2_begin = "NULL";
            $p_date3_begin = "NULL";
            $p_date4_begin = "NULL";
            $p_date5_begin = "NULL";
            $p_date6_begin = "NULL";
            $p_date7_begin = "NULL";
            $p_date8_begin = "NULL";
            $p_date9_begin = "NULL";
            $p_date10_begin = "NULL";
            $p_date11_begin = "NULL";
            $p_date12_begin = "NULL";

            $p_date1_end = "NULL";
            $p_date2_end = "NULL";
            $p_date3_end = "NULL";
            $p_date4_end = "NULL";
            $p_date5_end = "NULL";
            $p_date6_end = "NULL";
            $p_date7_end = "NULL";
            $p_date8_end = "NULL";
            $p_date9_end = "NULL";
            $p_date10_end = "NULL";
            $p_date11_end = "NULL";
            $p_date12_end = "NULL";

            $date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
            if ((!$date_test) || empty($p_date1)) {
                $_SESSION["c_error_msg"] .= "Bitte gültiges Datum für Termin 1 angeben.<br>";
            } else {
                $p_date1_begin = "STR_TO_DATE('$p_date1 " . $p_from1 . "','%d.%m.%Y %H:%i')";
                $p_date1_end = "STR_TO_DATE('$p_date1 " . $p_to1 . "','%d.%m.%Y %H:%i')";
            }
            $time_from = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_from1);
            if (!$time_from) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (von) für Termin 1 eingeben.<br>";
            }
            $time_to = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_to1);
            if (!$time_to) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (bis) für Termin 1 eingeben.<br>";
            }

            if (empty($_SESSION["c_error_msg"])) {
                $time_diff = $time_from->diff($time_to);
                $p_duration = $time_diff->i + 60 * $time_diff->h;
            }
            $p_begin = $p_date1_begin;
            $p_end = $p_date1_end;
            if (empty($p_date2)) {
                $p_date2 = "NULL";
            } else {
                $p_date2_begin = "STR_TO_DATE('$p_date2 " . $p_from2 . "','%d.%m.%Y %H:%i')";
                $p_date2_end = "STR_TO_DATE('$p_date2 " . $p_to2 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date2_end;
            }
            if (empty($p_date3)) {
                $p_date3 = "NULL";
            } else {
                if (!isset($p_date2) || $p_date2 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
                $p_date3_begin = "STR_TO_DATE('$p_date3 " . $p_from3 . "','%d.%m.%Y %H:%i')";
                $p_date3_end = "STR_TO_DATE('$p_date3 " . $p_to3 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date3_end;
            }
            if (empty($p_date4)) {
                $p_date4 = "NULL";
            } else {
                if (!isset($p_date3) || $p_date3 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
                $p_date4_begin = "STR_TO_DATE('$p_date4 " . $p_from4 . "','%d.%m.%Y %H:%i')";
                $p_date4_end = "STR_TO_DATE('$p_date4 " . $p_to4 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date4_end;
            }
            if (empty($p_date5)) {
                $p_date5 = "NULL";
            } else {
                if (!isset($p_date4) || $p_date4 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
                $p_date5_begin = "STR_TO_DATE('$p_date5 " . $p_from5 . "','%d.%m.%Y %H:%i')";
                $p_date5_end = "STR_TO_DATE('$p_date5 " . $p_to5 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date5_end;
            }
            if (empty($p_date6)) {
                $p_date6 = "NULL";
            } else {
                if (!isset($p_date5) || $p_date5 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
                $p_date6_begin = "STR_TO_DATE('$p_date6 " . $p_from6 . "','%d.%m.%Y %H:%i')";
                $p_date6_end = "STR_TO_DATE('$p_date6 " . $p_to6 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date6_end;
            }
            if (empty($p_date7)) {
                $p_date7 = "NULL";
            } else {
                if (!isset($p_date6) || $p_date6 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
                $p_date7_begin = "STR_TO_DATE('$p_date7 " . $p_from7 . "','%d.%m.%Y %H:%i')";
                $p_date7_end = "STR_TO_DATE('$p_date7 " . $p_to7 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date7_end;
            }
            if (empty($p_date8)) {
                $p_date8 = "NULL";
            } else {
                if (!isset($p_date7) || $p_date7 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
                $p_date8_begin = "STR_TO_DATE('$p_date8 " . $p_from8 . "','%d.%m.%Y %H:%i')";
                $p_date8_end = "STR_TO_DATE('$p_date8 " . $p_to8 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date8_end;
            }
            if (empty($p_date9)) {
                $p_date9 = "NULL";
            } else {
                if (!isset($p_date8) || $p_date8 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
                $p_date9_begin = "STR_TO_DATE('$p_date9 " . $p_from9 . "','%d.%m.%Y %H:%i')";
                $p_date9_end = "STR_TO_DATE('$p_date9 " . $p_to9 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date9_end;
            }
            if (empty($p_date10)) {
                $p_date10 = "NULL";
            } else {
                if (!isset($p_date9) || $p_date9 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
                $p_date10_begin = "STR_TO_DATE('$p_date10 " . $p_from10 . "','%d.%m.%Y %H:%i')";
                $p_date10_end = "STR_TO_DATE('$p_date10 " . $p_to10 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date10_end;
            }
            if (empty($p_date11)) {
                $p_date11 = "NULL";
            } else {
                if (!isset($p_date10) || $p_date10 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
                $p_date11_begin = "STR_TO_DATE('$p_date11 " . $p_from11 . "','%d.%m.%Y %H:%i')";
                $p_date11_end = "STR_TO_DATE('$p_date11 " . $p_to11 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date11_end;
            }
            if (empty($p_date12)) {
                $p_date12 = "NULL";
            } else {
                if (!isset($p_date11) || $p_date11 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
                $p_date12_begin = "STR_TO_DATE('$p_date12 " . $p_from12 . "','%d.%m.%Y %H:%i')";
                $p_date12_end = "STR_TO_DATE('$p_date12 " . $p_to12 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date12_end;
            }


        }
        if (empty($p_publishing)) $p_publishing = 1;
        if (empty($p_conf_text)) $p_conf_text = "";
        if ($p_not_on == "") unset($p_not_on);
        if (empty($p_not_on)) {
            $p_not_on = "NULL";
        } else {
            $p_not_on = "'$p_not_on'";
        }
        if (empty($p_precondition)) {
            $p_precondition = "NULL";
        } else {
            $p_precondition = "'$p_precondition'";
        }
        if (empty($p_textblock_mode) || !is_numeric($p_textblock_mode) || $p_textblock_mode != 1) $p_textblock_mode = 0;
        if (empty($p_textblock)) {
            $p_textblock = "NULL";
        } else {
            $p_textblock = "'$p_textblock'";
        }
        $db = $this->db_connect();

        $c_statement = "
			UPDATE as_courses
			   SET	   name = $p_name,
					   cat_id = $p_category,
					   subcat_id = $p_subcategory,
					   course_format_id = $p_course_format,
					   course_type_id = $p_course_type,
					   course_level_id = $p_course_level,
					   trainer_id = $p_trainer,
					   trainer_id2 = $p_trainer2,
					   location_id = $p_location,
					   max_count = $p_participant_count,
					   status = $p_status,
					   price = $p_price,
					   public_remark = $p_note1,
					   private_remark = $p_note2,
					   duration = $p_duration,
					   date1 = $p_date1_begin,
					   date2 = $p_date2_begin,
					   date3 = $p_date3_begin,
					   date4 = $p_date4_begin,
					   date5 = $p_date5_begin,
					   date6 = $p_date6_begin,
					   date7 = $p_date7_begin,
					   date8 = $p_date8_begin,
					   date9 = $p_date9_begin,
					   date10 = $p_date10_begin,
					   date11 = $p_date11_begin,
					   date12 = $p_date12_begin,
					   date1_end = $p_date1_end,
					   date2_end = $p_date2_end,
					   date3_end = $p_date3_end,
					   date4_end = $p_date4_end,
					   date5_end = $p_date5_end,
					   date6_end = $p_date6_end,
					   date7_end = $p_date7_end,
					   date8_end = $p_date8_end,
					   date9_end = $p_date9_end,
					   date10_end = $p_date10_end,
					   date11_end = $p_date11_end,
					   date12_end = $p_date12_end,
					   begin = $p_begin,
					   end = $p_end,
					   publishing = $p_publishing,
					   todo = $p_todo,
					   confirmation_text = '$p_conf_text',
					   not_on = $p_not_on,
					   precondition = $p_precondition,
					   textblock_mode = $p_textblock_mode,
					   textblock = $p_textblock
			WHERE course_id= $p_id;";


        $result = false;
        $_SESSION["c_success_msg"] = false;
        if (empty($_SESSION["c_error_msg"])) {
            $result = $db->query($c_statement);

            if (!$result) {
                $_SESSION["c_error_msg"] = $db->error . "<br><br>" . $c_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["c_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
                unset($_SESSION["c_no_reload_data"]);
                $db->close();
                return true;
            }
        }

    }

    public function db_update_actual_count($p_course_id)
    {

        $db = $this->db_connect();

        $c_statement = "
			UPDATE as_courses
			   SET	   registration_count = (SELECT count(1) from as_registrations r1
					   							WHERE r1.course_id = $p_course_id
												  AND r1.status IN (2,3)),
			   		   pre_reg_count	  = (SELECT count(1) from as_registrations r2
					   							WHERE r2.course_id = $p_course_id
												  AND r2.status IN (1,4,5))
			WHERE course_id= $p_course_id;";

        $result = $db->query($c_statement);

        if (!$result) echo $db->error;
        $db->close();
    }


    /*	public function db_change_actual_count( $p_course_id,
										    $p_count_change)
	{

		$db=$this->db_connect();

		$c_statement = "
			UPDATE as_courses
			   SET	   actual_count = actual_count +  $p_count_change
			WHERE course_id= $p_course_id;";

		$result = $db->query($c_statement);

		if(!$result) echo $db->error;
		$db->close();



	} */

    public function remove_old_event($event_id)
    {
        if (isset($event_id)) {
            $db = $this->db_connect();
            if ($statement = $db->prepare("UPDATE as_events SET status = 0 WHERE event_id=?")) {
                $statement->bind_param("i", $event_id);
                $statement->execute();
                $db->close();
            } else {
                echo "E-2035: Fehler: Laden der Standorte fehlgeschlagen.";
                echo "<br>" . $db->error;
                $db->close();
            }
        }
    }


    public
    function db_insert_new_course($p_name,
                                  $p_category,
                                  $p_subcategory,
                                  $p_course_format,
                                  $p_course_type,
                                  $p_course_level,
                                  $p_trainer,
                                  $p_trainer2,
                                  $p_location,
                                  $p_participant_count,
                                  $p_status,
                                  $p_price,
                                  $p_note1,
                                  $p_note2,
                                  $p_duration,
                                  $p_time,
                                  $p_date1,
                                  $p_date2,
                                  $p_date3,
                                  $p_date4,
                                  $p_date5,
                                  $p_date6,
                                  $p_date7,
                                  $p_date8,
                                  $p_date9,
                                  $p_date10,
                                  $p_date11,
                                  $p_date12,
                                  $p_times_equal,
                                  $p_from1,
                                  $p_from2,
                                  $p_from3,
                                  $p_from4,
                                  $p_from5,
                                  $p_from6,
                                  $p_from7,
                                  $p_from8,
                                  $p_from9,
                                  $p_from10,
                                  $p_from11,
                                  $p_from12,
                                  $p_to1,
                                  $p_to2,
                                  $p_to3,
                                  $p_to4,
                                  $p_to5,
                                  $p_to6,
                                  $p_to7,
                                  $p_to8,
                                  $p_to9,
                                  $p_to10,
                                  $p_to11,
                                  $p_to12,
                                  $p_publishing,
                                  $p_todo = false,
                                  $p_conf_text,
                                  $p_not_on,
                                  $p_precondition,
                                  $p_textblock_mode,
                                  $p_textblock,
                                  $p_create_single_courses = false)


    {
        // Validation
        if (empty($p_course_format)) $p_course_format = 'NULL';
        if (empty($p_course_type)) $p_course_type = 'NULL';
        if (empty($p_course_level)) $p_course_level = 'NULL';

        $_SESSION["c_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["c_error_msg"] .= "Bitte einen Kursnamen eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (empty($p_category)) {
            $p_category = "NULL";
        }
        if (empty($p_trainer)) {
            $_SESSION["c_error_msg"] .= "Bitte Trainer wählen.<br>";
            $p_trainer = "NULL";
        }
        if ($p_trainer == -1 && $p_status != 1) {
            $_SESSION["c_error_msg"] .= "Bitte Trainer wählen oder Status auf 'in Bearbeitung' setzen. <br>";
            $p_trainer = "NULL";
        }
        if (isset($p_trainer2) && $p_trainer2 == 'none') {
            $p_trainer2 = "NULL";
        }
        if (empty($p_trainer2)) {
            $p_trainer2 = "NULL";
        }
        if (empty($p_location)) {
            $p_location = "NULL";
            $_SESSION["c_error_msg"] .= "Bitte Standort wählen.<br>";
        }
        if (empty($p_participant_count)) {
            $p_participant_count = 8;
        }
        if (empty($p_price) || !is_numeric($p_price)) {
            $p_location = "NULL";
            $_SESSION["c_error_msg"] .= "Bitte gültigen Preis angeben.<br>";
        }
        if (empty($p_status) || $p_status == -1) $p_status = 1;
        if (empty($p_price)) $p_price = 100;
        if (empty($p_note1)) {
            $p_note1 = "NULL";
        } else {
            $p_note1 = "'" . $p_note1 . "'";
        }
        if (empty($p_note2)) {
            $p_note2 = "NULL";
        } else {
            $p_note2 = "'" . $p_note2 . "'";
        }
        if (empty($p_duration)) $p_duration = 75;
        if ($p_times_equal == 1) {


            if (empty($p_duration) || !is_numeric($p_duration)) {
                $p_location = "NULL";
                $_SESSION["c_error_msg"] .= "Bitte Kursdauer in Minuten angeben.<br>";
            }

            if (empty($p_duration)) $p_duration = 75;
            $p_time_end = $p_time;

            $time_as_date = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_time);
            if (!$time_as_date) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit eingeben.<br>";
            } else {
                $p_time_end = $time_as_date->add(new DateInterval('PT' . $p_duration . 'M'));
                $p_time_end = $p_time_end->format("H:i");
            }

            $date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
            if ((!$date_test) || empty($p_date1)) {
                $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 1 angeben.<br>";
                $p_begin = 'NULL';
                $p_end = 'NULL';
            } else {
                $p_date1_end = "STR_TO_DATE('" . $p_date1 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date1_begin = "STR_TO_DATE('" . $p_date1 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_begin = $p_date1_begin;
                $p_end = $p_date1_begin;
            }

            if (empty($p_date2)) {
                $p_date2_begin = "NULL";
                $p_date2_end = "NULL";
            } else {
                $p_date2_end = "STR_TO_DATE('" . $p_date2 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date2_begin = "STR_TO_DATE('" . $p_date2 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date2_end;
            }
            if (empty($p_date3)) {
                $p_date3_begin = "NULL";
                $p_date3_end = "NULL";
            } else {
                if (!isset($p_date2) || $p_date2 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
                $p_date3_end = "STR_TO_DATE('" . $p_date3 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date3_begin = "STR_TO_DATE('" . $p_date3 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date3_end;
            }
            if (empty($p_date4)) {
                $p_date4_begin = "NULL";
                $p_date4_end = "NULL";
            } else {
                if (!isset($p_date3) || $p_date3 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
                $p_date4_end = "STR_TO_DATE('" . $p_date4 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date4_begin = "STR_TO_DATE('" . $p_date4 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date4_end;
            }
            if (empty($p_date5)) {
                $p_date5_begin = "NULL";
                $p_date5_end = "NULL";
            } else {
                if (!isset($p_date4) || $p_date4 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
                $p_date5_end = "STR_TO_DATE('" . $p_date5 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date5_begin = "STR_TO_DATE('" . $p_date5 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date5_end;
            }
            if (empty($p_date6)) {
                $p_date6_begin = "NULL";
                $p_date6_end = "NULL";
            } else {
                if (!isset($p_date5) || $p_date5 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
                $p_date6_end = "STR_TO_DATE('" . $p_date6 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date6_begin = "STR_TO_DATE('" . $p_date6 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date6_end;
            }
            if (empty($p_date7)) {
                $p_date7_begin = "NULL";
                $p_date7_end = "NULL";
            } else {
                if (!isset($p_date6) || $p_date6 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
                $p_date7_end = "STR_TO_DATE('" . $p_date7 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date7_begin = "STR_TO_DATE('" . $p_date7 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date7_end;
            }
            if (empty($p_date8)) {
                $p_date8_begin = "NULL";
                $p_date8_end = "NULL";
            } else {
                if (!isset($p_date7) || $p_date7 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
                $p_date8_end = "STR_TO_DATE('" . $p_date8 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date8_begin = "STR_TO_DATE('" . $p_date8 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date8_end;
            }
            if (empty($p_date9)) {
                $p_date9_begin = "NULL";
                $p_date9_end = "NULL";
            } else {
                if (!isset($p_date8) || $p_date8 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
                $p_date9_end = "STR_TO_DATE('" . $p_date9 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date9_begin = "STR_TO_DATE('" . $p_date9 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date9_end;
            }
            if (empty($p_date10)) {
                $p_date10_begin = "NULL";
                $p_date10_end = "NULL";
            } else {
                if (!isset($p_date9) || $p_date9 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
                $p_date10_end = "STR_TO_DATE('" . $p_date10 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date10_begin = "STR_TO_DATE('" . $p_date10 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date10_end;
            }
            if (empty($p_date11)) {
                $p_date11_begin = "NULL";
                $p_date11_end = "NULL";
            } else {
                if (!isset($p_date10) || $p_date10 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
                $p_date11_end = "STR_TO_DATE('" . $p_date11 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date11_begin = "STR_TO_DATE('" . $p_date11 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date11_end;
            }
            if (empty($p_date12)) {
                $p_date12_begin = "NULL";
                $p_date12_end = "NULL";
            } else {
                if (!isset($p_date11) || $p_date11 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
                $p_date12_end = "STR_TO_DATE('" . $p_date12 . " " . $p_time_end . "','%d.%m.%Y %H:%i')";
                $p_date12_begin = "STR_TO_DATE('" . $p_date12 . " " . $p_time . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date12_end;
            }
        } else {  // times not equal


            if (strlen($p_from1) == 2) $p_from1 = $p_from1 . ':00';
            if (strlen($p_from2) == 2) $p_from2 = $p_from2 . ':00';
            if (strlen($p_from3) == 2) $p_from3 = $p_from3 . ':00';
            if (strlen($p_from4) == 2) $p_from4 = $p_from4 . ':00';
            if (strlen($p_from5) == 2) $p_from5 = $p_from5 . ':00';
            if (strlen($p_from6) == 2) $p_from6 = $p_from6 . ':00';
            if (strlen($p_from7) == 2) $p_from7 = $p_from7 . ':00';
            if (strlen($p_from8) == 2) $p_from8 = $p_from8 . ':00';
            if (strlen($p_from9) == 2) $p_from9 = $p_from9 . ':00';
            if (strlen($p_from10) == 2) $p_from10 = $p_from10 . ':00';
            if (strlen($p_from11) == 2) $p_from11 = $p_from11 . ':00';
            if (strlen($p_from12) == 2) $p_from12 = $p_from12 . ':00';

            if (strlen($p_to1) == 2) $p_to1 = $p_to1 . ':00';
            if (strlen($p_to2) == 2) $p_to2 = $p_to2 . ':00';
            if (strlen($p_to3) == 2) $p_to3 = $p_to3 . ':00';
            if (strlen($p_to4) == 2) $p_to4 = $p_to4 . ':00';
            if (strlen($p_to5) == 2) $p_to5 = $p_to5 . ':00';
            if (strlen($p_to6) == 2) $p_to6 = $p_to6 . ':00';
            if (strlen($p_to7) == 2) $p_to7 = $p_to7 . ':00';
            if (strlen($p_to8) == 2) $p_to8 = $p_to8 . ':00';
            if (strlen($p_to9) == 2) $p_to9 = $p_to9 . ':00';
            if (strlen($p_to10) == 2) $p_to10 = $p_to10 . ':00';
            if (strlen($p_to11) == 2) $p_to11 = $p_to11 . ':00';
            if (strlen($p_to12) == 2) $p_to12 = $p_to12 . ':00';

            if (empty($p_from2) && !empty($p_date2)) $p_from2 = $p_from1;
            if (empty($p_from3) && !empty($p_date3)) $p_from3 = $p_from2;
            if (empty($p_from4) && !empty($p_date4)) $p_from4 = $p_from3;
            if (empty($p_from5) && !empty($p_date5)) $p_from5 = $p_from4;
            if (empty($p_from6) && !empty($p_date6)) $p_from6 = $p_from5;
            if (empty($p_from7) && !empty($p_date7)) $p_from7 = $p_from6;
            if (empty($p_from8) && !empty($p_date8)) $p_from8 = $p_from7;
            if (empty($p_from9) && !empty($p_date9)) $p_from9 = $p_from8;
            if (empty($p_from10) && !empty($p_date10)) $p_from10 = $p_from9;
            if (empty($p_from11) && !empty($p_date11)) $p_from11 = $p_from10;
            if (empty($p_from12) && !empty($p_date12)) $p_from12 = $p_from11;

            if (empty($p_to2) && !empty($p_date2)) $p_to2 = $p_to1;
            if (empty($p_to3) && !empty($p_date3)) $p_to3 = $p_to2;
            if (empty($p_to4) && !empty($p_date4)) $p_to4 = $p_to3;
            if (empty($p_to5) && !empty($p_date5)) $p_to5 = $p_to4;
            if (empty($p_to6) && !empty($p_date6)) $p_to6 = $p_to5;
            if (empty($p_to7) && !empty($p_date7)) $p_to7 = $p_to6;
            if (empty($p_to8) && !empty($p_date8)) $p_to8 = $p_to7;
            if (empty($p_to9) && !empty($p_date9)) $p_to9 = $p_to8;
            if (empty($p_to10) && !empty($p_date10)) $p_to10 = $p_to9;
            if (empty($p_to11) && !empty($p_date11)) $p_to11 = $p_to10;
            if (empty($p_to12) && !empty($p_date12)) $p_to12 = $p_to11;

            $p_date1_begin = "NULL";
            $p_date2_begin = "NULL";
            $p_date3_begin = "NULL";
            $p_date4_begin = "NULL";
            $p_date5_begin = "NULL";
            $p_date6_begin = "NULL";
            $p_date7_begin = "NULL";
            $p_date8_begin = "NULL";
            $p_date9_begin = "NULL";
            $p_date10_begin = "NULL";
            $p_date11_begin = "NULL";
            $p_date12_begin = "NULL";

            $p_date1_end = "NULL";
            $p_date2_end = "NULL";
            $p_date3_end = "NULL";
            $p_date4_end = "NULL";
            $p_date5_end = "NULL";
            $p_date6_end = "NULL";
            $p_date7_end = "NULL";
            $p_date8_end = "NULL";
            $p_date9_end = "NULL";
            $p_date10_end = "NULL";
            $p_date11_end = "NULL";
            $p_date12_end = "NULL";

            $date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
            if ((!$date_test) || empty($p_date1)) {
                $_SESSION["c_error_msg"] .= "Bitte gültiges Datum für Termin 1 angeben.<br>";
            } else {
                $p_date1_begin = "STR_TO_DATE('$p_date1 " . $p_from1 . "','%d.%m.%Y %H:%i')";
                $p_date1_end = "STR_TO_DATE('$p_date1 " . $p_to1 . "','%d.%m.%Y %H:%i')";
            }
            $time_from = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_from1);
            if (!$time_from) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (von) für Termin 1 eingeben.<br>";
            }
            $time_to = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_to1);
            if (!$time_to) {
                $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (bis) für Termin 1 eingeben.<br>";
            }

            for ($i = 2; $i <= 12; $i++) {

                $var_name_date = 'p_date' . $i;
                $var_name_from = 'p_from' . $i;
                $var_name_to = 'p_to' . $i;

                if (empty($$var_name_date)) continue;

                $date_test2 = DateTime::createFromFormat('d.m.Y', $$var_name_date);
                if ((!$date_test2) || empty($$var_name_date)) {
                    $_SESSION["c_error_msg"] .= "Bitte gültiges Datum für Termin $i angeben.<br>";
                }
                $time_from2 = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $$var_name_from);
                if (!$time_from2) {
                    $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (von) für Termin $i eingeben.<br>";
                }
                $time_to2 = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $$var_name_to);
                if (!$time_to2) {
                    $_SESSION["c_error_msg"] .= "Bitte gültige Uhrzeit (bis) für Termin $i eingeben.<br>";
                }

            }


            if (empty($_SESSION["c_error_msg"])) {
                $time_diff = $time_from->diff($time_to);
                $p_duration = $time_diff->i + 60 * $time_diff->h;
            }
            $p_begin = $p_date1_begin;
            $p_end = $p_date1_end;
            if (empty($p_date2)) {
                $p_date2 = "NULL";
            } else {
                $p_date2_begin = "STR_TO_DATE('$p_date2 " . $p_from2 . "','%d.%m.%Y %H:%i')";
                $p_date2_end = "STR_TO_DATE('$p_date2 " . $p_to2 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date2_end;
            }
            if (empty($p_date3)) {
                $p_date3 = "NULL";
            } else {
                if (!isset($p_date2) || $p_date2 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
                $p_date3_begin = "STR_TO_DATE('$p_date3 " . $p_from3 . "','%d.%m.%Y %H:%i')";
                $p_date3_end = "STR_TO_DATE('$p_date3 " . $p_to3 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date3_end;
            }
            if (empty($p_date4)) {
                $p_date4 = "NULL";
            } else {
                if (!isset($p_date3) || $p_date3 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
                $p_date4_begin = "STR_TO_DATE('$p_date4 " . $p_from4 . "','%d.%m.%Y %H:%i')";
                $p_date4_end = "STR_TO_DATE('$p_date4 " . $p_to4 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date4_end;
            }
            if (empty($p_date5)) {
                $p_date5 = "NULL";
            } else {
                if (!isset($p_date4) || $p_date4 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
                $p_date5_begin = "STR_TO_DATE('$p_date5 " . $p_from5 . "','%d.%m.%Y %H:%i')";
                $p_date5_end = "STR_TO_DATE('$p_date5 " . $p_to5 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date5_end;
            }
            if (empty($p_date6)) {
                $p_date6 = "NULL";
            } else {
                if (!isset($p_date5) || $p_date5 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
                $p_date6_begin = "STR_TO_DATE('$p_date6 " . $p_from6 . "','%d.%m.%Y %H:%i')";
                $p_date6_end = "STR_TO_DATE('$p_date6 " . $p_to6 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date6_end;
            }
            if (empty($p_date7)) {
                $p_date7 = "NULL";
            } else {
                if (!isset($p_date6) || $p_date6 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
                $p_date7_begin = "STR_TO_DATE('$p_date7 " . $p_from7 . "','%d.%m.%Y %H:%i')";
                $p_date7_end = "STR_TO_DATE('$p_date7 " . $p_to7 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date7_end;
            }
            if (empty($p_date8)) {
                $p_date8 = "NULL";
            } else {
                if (!isset($p_date7) || $p_date7 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
                $p_date8_begin = "STR_TO_DATE('$p_date8 " . $p_from8 . "','%d.%m.%Y %H:%i')";
                $p_date8_end = "STR_TO_DATE('$p_date8 " . $p_to8 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date8_end;
            }
            if (empty($p_date9)) {
                $p_date9 = "NULL";
            } else {
                if (!isset($p_date8) || $p_date8 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
                $p_date9_begin = "STR_TO_DATE('$p_date9 " . $p_from9 . "','%d.%m.%Y %H:%i')";
                $p_date9_end = "STR_TO_DATE('$p_date9 " . $p_to9 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date9_end;
            }
            if (empty($p_date10)) {
                $p_date10 = "NULL";
            } else {
                if (!isset($p_date9) || $p_date9 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
                $p_date10_begin = "STR_TO_DATE('$p_date10 " . $p_from10 . "','%d.%m.%Y %H:%i')";
                $p_date10_end = "STR_TO_DATE('$p_date10 " . $p_to10 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date10_end;
            }
            if (empty($p_date11)) {
                $p_date11 = "NULL";
            } else {
                if (!isset($p_date10) || $p_date10 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
                $p_date11_begin = "STR_TO_DATE('$p_date11 " . $p_from11 . "','%d.%m.%Y %H:%i')";
                $p_date11_end = "STR_TO_DATE('$p_date11 " . $p_to11 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date11_end;
            }
            if (empty($p_date12)) {
                $p_date12 = "NULL";
            } else {
                if (!isset($p_date11) || $p_date11 == "NULL") $_SESSION["c_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
                $p_date12_begin = "STR_TO_DATE('$p_date12 " . $p_from12 . "','%d.%m.%Y %H:%i')";
                $p_date12_end = "STR_TO_DATE('$p_date12 " . $p_to12 . "','%d.%m.%Y %H:%i')";
                $p_end = $p_date12_end;
            }


        }
        if (empty($p_publishing)) $p_publishing = 1;
        if (empty($p_conf_text)) $p_conf_text = "";
        if ($p_not_on == "") unset($p_not_on);
        if (empty($p_not_on)) {
            $p_not_on = "NULL";
        } else {
            $p_not_on = "'$p_not_on'";
        }
        if (empty($p_precondition)) {
            $p_precondition = "NULL";
        } else {
            $p_precondition = "'$p_precondition'";
        }
        if (empty($p_textblock_mode) || !is_numeric($p_textblock_mode) || $p_textblock_mode != 1) $p_textblock_mode = 0;
        if (empty($p_textblock)) {
            $p_textblock = "NULL";
        } else {
            $p_textblock = "'$p_textblock'";
        }

        $db = $this->db_connect();

        if (!$p_create_single_courses) {

            $c_statement = "
			INSERT INTO as_courses(name,
								   cat_id,
								   subcat_id,
								   course_format_id,
								   course_type_id,
								   course_level_id,
								   trainer_id,
								   trainer_id2,
								   location_id,
								   max_count,
								   status,
								   price,
								   public_remark,
								   private_remark,
								   duration,
								   date1,
								   date2,
								   date3,
								   date4,
								   date5,
								   date6,
								   date7,
								   date8,
								   date9,
								   date10,
								   date11,
								   date12,
								   date1_end,
								   date2_end,
								   date3_end,
								   date4_end,
								   date5_end,
								   date6_end,
								   date7_end,
								   date8_end,
								   date9_end,
								   date10_end,
								   date11_end,
								   date12_end,
								   begin,
								   end,
								   publishing,
								   todo,
								   confirmation_text,
								   not_on,
								   precondition,
								   textblock_mode,
								   textblock)
								   
			VALUES 			($p_name,
								   $p_category,
								   $p_subcategory,
									 $p_course_format,
									 $p_course_type,
									 $p_course_level,
								   $p_trainer,
								   $p_trainer2,
								   $p_location,
								   $p_participant_count,
								   $p_status,
								   $p_price,
								   $p_note1,
								   $p_note2,
								   $p_duration,
								   $p_date1_begin,
								   $p_date2_begin,
								   $p_date3_begin,
								   $p_date4_begin,
								   $p_date5_begin,
								   $p_date6_begin,
								   $p_date7_begin,
								   $p_date8_begin,
								   $p_date9_begin,
								   $p_date10_begin,
								   $p_date11_begin,
								   $p_date12_begin,
								   $p_date1_end,
								   $p_date2_end,
								   $p_date3_end,
								   $p_date4_end,
								   $p_date5_end,
								   $p_date6_end,
								   $p_date7_end,
								   $p_date8_end,
								   $p_date9_end,
								   $p_date10_end,
								   $p_date11_end,
								   $p_date12_end,
								   $p_begin,
								   $p_end,
								   $p_publishing,
								   $p_todo,
								   '$p_conf_text',
								   $p_not_on,
								   $p_precondition,
								   $p_textblock_mode,
								   $p_textblock);";
        } else {
            $c_statement = "
			INSERT INTO as_courses(name,
								   cat_id,
								   subcat_id,
								   course_format_id,
								   course_type_id,
								   course_level_id,
								   trainer_id,
								   trainer_id2,
								   location_id,
								   max_count,
								   status,
								   price,
								   public_remark,
								   private_remark,
								   duration,
								   date1,
								   date1_end,
								   begin,
								   end,
								   publishing,
								   todo,
								   confirmation_text,
								   not_on,
								   precondition,
								   textblock_mode,
								   textblock)
								   
			VALUES 				  ($p_name,
								   $p_category,
								   $p_subcategory,
									 $p_course_format,
									 $p_course_type,
									 $p_course_level,
								   $p_trainer,
								   $p_trainer2,
								   $p_location,
								   $p_participant_count,
								   $p_status,
								   $p_price,
								   $p_note1,
								   $p_note2,
								   $p_duration,
								   ###_date1_begin###,
								   ###_date1_end###,
								   ###_begin###,
								   ###_end###,
								   $p_publishing,
								   $p_todo,
								   '$p_conf_text',
								   $p_not_on,
								   $p_precondition,
								   $p_textblock_mode,
								   $p_textblock);";
        }


        $result = false;
        $_SESSION["c_success_msg"] = false;
        if (empty($_SESSION["c_error_msg"])) {
            if ($p_create_single_courses == false) {

                $result = $db->query($c_statement);

                if (!$result) {
                    $_SESSION["c_error_msg"] = $db->error . "<br><br>" . $c_statement;
                    $db->close();
                    return false;
                } else {
                    $_SESSION["course_id"] = $db->insert_id;
                    $_SESSION["c_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
                    $db->close();
                    return true;
                }

            } else { // create multiple single date courses
                $course_id_listing = '';
                for ($i = 1; $i <= 12; $i++) {
                    $var_name_begin = 'p_date' . $i . '_begin';
                    $var_name_end = 'p_date' . $i . '_end';
                    if (!isset($$var_name_begin) || $$var_name_begin == 'NULL') continue;
                    if (!isset($$var_name_end) || $$var_name_end == 'NULL') continue;

                    $result = $db->query(str_replace('###_end###', $$var_name_end, str_replace('###_begin###', $$var_name_begin, str_replace('###_date1_begin###', $$var_name_begin, str_replace('###_date1_end###', $$var_name_end, $c_statement)))));
                    if (!$result) break;
                    $course_id_listing .= $db->insert_id . ',';
                }

                if (!$result) {
                    $_SESSION["c_error_msg"] = $db->error . "<br><br>" . $c_statement;
                    $db->close();
                    return false;
                } else {
                    $_SESSION["course_id"] = $db->insert_id;
                    $_SESSION["c_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
                    $db->close();
                    $_SESSION['filter_course_number'] = trim($course_id_listing, ',');
                    return true;
                }

            }
        }

    }

    public
    function db_count_todo()
    {  // Ermittle Anzahl der "Offenen Aufgaben"


        $db = $this->db_connect();

        $result = $db->query(

            "SELECT count(1) AS count
					   FROM
							as_courses c
					  WHERE c.status IN (1,2,3)
					    AND c.todo = TRUE
					  LIMIT 2000");


        if (!$result) return "?";

        $line = $result->fetch_array();

        return $line["count"];

    }

    public
    function db_publish_now()
    {

        $db = $this->db_connect();

        $result = $db->query(

            "UPDATE as_courses
						SET publishing = 2
					  WHERE publishing = 1
					    AND status IN (2);");

        if ($db->affected_rows == 1) {
            echo "Es wurde 1 Kurs veröffentlicht.";
        } else {
            echo "Es wurden " . $db->affected_rows . " Kurse veröffentlicht.";
        }

        $db->close();
    }

    public
    function db_check_if_kid_course($p_course_id)
    {

        $is_kid_course = 0;

        try {
            $db = $this->db_connect();
            $result = $db->query("
								SELECT COALESCE(sca.is_kid_course, 0) as is_kid_course
								  FROM as_courses c
								 LEFT JOIN as_subcategories sca	ON sca.subcat_id = c.subcat_id
								 WHERE c.course_id = $p_course_id;
								  ");
            if ($result) {
                $line = $result->fetch_array();
                if ($line && isset($line["is_kid_course"])) {
                    $is_kid_course = $line["is_kid_course"];
                }
            }

        } catch (exception $e) {
        }
        return $is_kid_course;
    }
}

class DB_Functions_Students extends DB_Connect
{


    public function db_load_table_students($p_prename, $p_surname, $p_email, $p_status)
    {

        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2224: No Permission to read Data. Ask your Administrator";
            return false;
        }


        $db = $this->db_connect();


        if (empty($p_prename) || $p_prename == "*") {
            $p_prename = "##all";
        } else {
            $p_prename = '%' . str_replace('*', '%', $p_prename) . '%';
        }


        if (empty($p_surname) || $p_surname == "*") {
            $p_surname = "##all";
        } else {
            $p_surname = '%' . str_replace('*', '%', $p_surname) . '%';
        }
        if (empty($p_email) || $p_email == "*") {
            $p_email = "##all";
        } else {
            $p_email = '%' . str_replace('*', '%', $p_email) . '%';
        }
        if (!isset($p_status)) $p_status = -2;


        $result = $db->query(

            "SELECT student_id,
					prename,
					surname,
					email,
					student_remark,
					newsletter,
					status,
					date_format(mod_dat, '%d.%m.%Y %H:%i') as mod_dat1
			   from as_students s
			  WHERE ('$p_prename' = '##all'
			  			OR s.prename LIKE '$p_prename')
			    AND ('$p_surname' = '##all'
			  			OR s.surname LIKE '$p_surname')
			    AND ('$p_email' = '##all'
			  			OR s.email LIKE '$p_email')
			    AND ($p_status = -2
			  			OR s.status = $p_status)
		      ORDER BY s.mod_dat DESC
		      LIMIT 2000;");


        if (!$result) echo $db->error;

        echo "<div class='students-list-table'><table>\n
		      <tr>";

        //if ($p_only_last_modified) echo "<th>Änderungsdatum</th>";


        echo "<th>Status</th>"
            . "<th style='min-width: 110px;'>Vorname</th>"
            . "<th style='min-width: 110px;'>Nachname</th>"
            . "<th style='min-width: 260px;'>E-Mail</th>"
            . "<th>Newsletter</th>"
            . "<th style='min-width: 260px;'>Teilnehmer- Vermerk</div></th>"
            . "</tr>\n";


        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 1:  // aktiviert
                    $line["status"] = "<div style='color: green'>✔</div>";
                    break;
                case 2:  // deaktiviert
                    $line["status"] = "<div style='color: red'>✖</div>";
                    break;
                case 3:  // fusioniert
                    $line["status"] = "<div style='color: orange'>➥</div>";
                    break;
                case 4:  // gesperrt
                    $line["status"] = "<div style='color: red'>✖✖</div>";
                    break;
                case 5:  // gesperrt
                    $line["status"] = "<div style='color: red'>@</div>";
                    break;
                case 6:  // Name unvollständig
                    $line["status"] = "<div style='color: blue'>✔?</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }
            switch ($line["newsletter"]) {
                case 1:  // aktiviert
                    $line["newsletter"] = "<div style='color: green'>✔</div>";
                    break;
                case 0:  // deaktiviert
                    $line["newsletter"] = "<div style='color: rgba(255, 0, 0, 0)'>✖</div>";
                    break;
                default:
                    $line["newsletter"] = "?";
                    break;
            }


            echo "<tr student_id='" . htmlspecialchars($line["student_id"]) . "'>";

            //if ($p_only_last_modified) echo "<td><div>" . htmlspecialchars($line["mod_dat1"]) . "</div></td>";


            echo "    <td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["prename"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . htmlspecialchars($line["surname"]) . "</div></td>"
                . "<td><div class='td-overflow email-text'>" . htmlspecialchars($line["email"]) . "</div></td>"
                . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["newsletter"] . "</div></td>"
                . "<td style='min-width: 200px; max-width: 400px'><div class='td-overflow'>" . $line["student_remark"] . "</div></td>"
                . "</tr>\n";

        }
        echo "</table></div>";


        if ($result->num_rows === 0) echo '<br>Keine Teilnehmer gefunden, bitte andere Suchoptionen wählen.<br><br> Empfohlene Suchoption: <b>Status: alle</b>';

        $db->close();

    }

    public function db_load_student_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT student_id,
						prename,
						surname,
						email,
						student_remark,
						COALESCE(newsletter, '0') AS newsletter,
						status,
						date_format(mod_dat, '%d.%m.%Y %H:%i') AS mod_dat1,
						merged_to
				   FROM as_students
			      WHERE student_id = " . $p_id . ";");

        if (!$result) echo $db->error;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            echo '<br>Kein Teilnehmer gefunden.';
            $db->close();
            return;
        } else {

            $_SESSION["s_prename"] = $line["prename"];
            $_SESSION["s_surname"] = $line["surname"];
            $_SESSION["s_email"] = $line["email"];
            $_SESSION["s_student_remark"] = $line["student_remark"];
            $_SESSION["s_status"] = $line["status"];
            $_SESSION["s_mod_dat"] = $line["mod_dat1"];
            $_SESSION["s_newsletter"] = $line["newsletter"];
            $_SESSION["s_merged_to"] = $line["merged_to"];
        }
        $db->close();

    }


    public function db_update_student($p_id,
                                      $p_prename,
                                      $p_surname,
                                      $p_email,
                                      $p_newsletter,
                                      $p_status,
                                      $p_merged_to,
                                      $p_student_remark,
                                      $p_search_code)
    {

        // Validation

        $_SESSION["s_error_msg"] = "";
        if (empty($p_prename)) {
            $_SESSION["s_error_msg"] .= "Bitte einen Vornamen eingeben.<br>";
        } else {
            $p_prename = "'" . $p_prename . "'";
        }
        if (empty($p_surname)) {
            $_SESSION["s_error_msg"] .= "Bitte einen Nachnamen eingeben.<br>";
        } else {
            $p_surname = "'" . $p_surname . "'";
        }
        if (empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["s_error_msg"] .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
        } else {
            $p_email = "'" . $p_email . "'";
        }
        if (empty($p_status)) $p_status = '1';

        if ($p_status == 3) {

            $check = $this->db_get_student_with_email($p_merged_to);

            if (!(isset($check["status"]) && $check["status"] == 1)) {
                $_SESSION["s_error_msg"] .= "Bitte für die Fusionierung eine E-Mail- Adresse zu einem aktivierten Teilnehmer angeben.<br>";
                $p_merged_to = "''";
            } else {
                $p_merged_to = "'" . $p_merged_to . "'";
            }
        } else $p_merged_to = "''";
        if (empty($p_student_remark)) {
            $p_student_remark = "''";
        } else {
            $p_student_remark = "'" . $p_student_remark . "'";
        }

        $db = $this->db_connect();

        $s_statement = "
			UPDATE as_students
			   SET	   prename = $p_prename,
					   surname = $p_surname,
					   email = $p_email,
					   student_remark = $p_student_remark,
					   newsletter = $p_newsletter,
					   status = $p_status,
					   merged_to = $p_merged_to,
					   mod_dat = now()
			WHERE student_id= $p_id;";

        $result = false;
        $_SESSION["s_success_msg"] = false;
        if (empty($_SESSION["s_error_msg"])) {
            $result = $db->query($s_statement);
            if (!$result) {
                $_SESSION["s_error_msg"] = $db->error;
                $db->close();
                return false;
            } else {
                $_SESSION["s_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }


    public function db_insert_new_student($p_prename,
                                          $p_surname,
                                          $p_email,
                                          $p_newsletter,
                                          $p_status,
                                          $p_merged_to,
                                          $p_student_remark,
                                          $p_search_code)
    {


        // Validation

        $_SESSION["s_error_msg"] = "";
        if (empty($p_prename)) {
            $_SESSION["s_error_msg"] .= "Bitte einen Vornamen eingeben.<br>";
        } else {
            $p_prename = "'" . $p_prename . "'";
        }
        if (empty($p_surname)) {
            $_SESSION["s_error_msg"] .= "Bitte einen Nachnamen eingeben.<br>";
        } else {
            $p_surname = "'" . $p_surname . "'";
        }
        if (empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["s_error_msg"] .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
        } else {
            $p_email = "'" . $p_email . "'";
        }
        if (empty($p_status)) $p_status = '1';

        if ($p_status == 3) {

            $check = $this->db_get_student_with_email($p_merged_to);

            if (!(isset($check["status"]) && $check["status"] != 3 && $check["status"] != 4)) {
                $_SESSION["s_error_msg"] .= "Bitte für die Fusionierung eine E-Mail- Adresse zu einem gültigen Teilnehmer angeben.<br>";
                $p_merged_to = "''";
            }
            $p_merged_to = "'" . $p_merged_to . "'";
        } else $p_merged_to = "''";

        if (empty($p_newsletter) || $p_newsletter != 1) {
            $p_newsletter = "0";
        } else {
            $p_newsletter = "1";
        }
        if (empty($p_student_remark)) {
            $p_student_remark = "''";
        } else {
            $p_student_remark = "'" . $p_student_remark . "'";
        }
        if (empty($p_search_code)) {
            $p_search_code = "''";
        } else {
            $p_search_code = "'" . $p_search_code . "'";
        }

        $db = $this->db_connect();

        $s_statement =

            "
			INSERT INTO as_students(prename,
								   surname,
								   email,
								   newsletter,
								   status,
								   merged_to,
								   student_remark,
								   search_code)
								   
			VALUES 				  ($p_prename,
								   $p_surname,
								   $p_email,
								   $p_newsletter,
								   $p_status,
								   $p_merged_to,
								   $p_student_remark,
								   $p_search_code);";


        $result = false;
        $_SESSION["s_success_msg"] = false;
        if (empty($_SESSION["s_error_msg"])) {
            $result = $db->query($s_statement);
            if (!$result) {
                $_SESSION["s_error_msg"] = $db->error;
                $db->close();
                return false;
            } else {
                $_SESSION["student_id"] = $db->insert_id;
                $_SESSION["s_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_generate_newsletter_addresses()
    {
        $db = $this->db_connect();
        $result = $db->query(

            "SELECT email
			  FROM as_students
			 WHERE newsletter = 1
			   AND status = 1
			 ORDER BY email;");

        if (!$result) echo $db->error;
        $newsletter = '';

        while ($line = $result->fetch_array()) {
            $newsletter .= $line["email"] . ';
';
        }
        $db->close();
        return $newsletter;
    }

    public function db_get_student_with_email($p_email)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "SELECT student_id,
					status,
					email
			   FROM as_students
			  WHERE email = '$p_email'
			    AND status != 3
			 UNION
			 SELECT linked_students.student_id,
			 		linked_students.status,
			 		linked_students.email
			  FROM  as_students as origin_students,
			  		as_students as linked_students
			  WHERE origin_students.email = '$p_email'
			    AND origin_students.status = 3
			    AND origin_students.merged_to = linked_students.email
			    AND linked_students.status != 3;");

        if (!$result) echo $db->error;

        if ($result->num_rows === 0) {

            $db->close();
            return false;
        }

        $line = $result->fetch_array();
        $db->close();
        return array('student_id' => $line["student_id"],
            'email' => $line["email"],
            'status' => $line["status"]);


    }


    public function db_student_is_registrated($p_student_id,
                                              $p_course_id)
    {

        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		
			SELECT 1 AS result
			  FROM as_registrations
			 WHERE course_id = ?
			   AND student_id = ?;")
        ) {
            $pre_stmt->bind_param("ii", $p_course_id,
                $p_student_id);
            $pre_stmt->execute();
            $pre_stmt->store_result();

            if ($pre_stmt->num_rows === 0) {
                $db->close();
                return false;
            } else {
                $db->close();
                return true;
            }
        } else {
            echo "E-7439: Fehler";
            return true;
        }
    }
}

class DB_Functions_Registrations extends DB_Connect
{


    public function db_insert_new_registration($p_course_id,
                                               $p_student_id,
                                               $p_status,
                                               $p_ranking_options,
                                               $voucher)
    {

        $db = $this->db_connect();

        $next_rank_position = $this->db_get_next_rank_position($p_course_id);

        if ($pre_stmt = $db->prepare("
		
			INSERT INTO as_registrations (course_id,
										  student_id,
										  status,
										  rank,
										  voucher)
										  
			VALUES (?, ?, ?, ?, ?)")
        ) {
            $pre_stmt->bind_param("iiiis", $p_course_id,
                $p_student_id,
                $p_status,
                $next_rank_position,
                $voucher);
            $pre_stmt->execute();
            if ($pre_stmt->affected_rows == 1) {

                global $db_functions;
                $db_functions->courses->db_update_actual_count($p_course_id, 1);
            }

            $db->close();
            return true;

        } else {
            echo "E-2033: Fehler: insert failed";
            $db->close();
            return false;
        }


        $db->close();
    }

    public function db_get_next_rank_position($p_course_id)
    {

        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		
			SELECT max(rank)+1 AS next_position
			  FROM (SELECT 0 AS rank
			  		  FROM dual
			  		UNION
					SELECT rank
			  		  FROM as_registrations
			 		 WHERE course_id = ?) AS r;")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($next_position);
            $pre_stmt->fetch();
            $db->close();
            return $next_position;

        } else {
            echo "E-2033: Fehler: Positionierung konnte nicht ermittelt werden.";
            return false;
        }
        $db->close();
    }


    public function db_load_table_registrations($p_course_id, $p_edit_id)
    {


        if (!(isset($p_edit_id))) $p_edit_id = 0;
        if (!($_SESSION["user_is_organizer"] == "1" ||
            $_SESSION["user_is_admin"] == "1")
        ) {
            echo "E-2221: No Permission to read Data. Ask your Administrator";
            return;
        }

        global $db_functions;
        $is_kid_course = $db_functions->courses->db_check_if_kid_course($p_course_id);

        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("
		
			SELECT s.prename,
				   s.surname,
				   s.email,
				   r.rank,
				   r.status,
				   DATE_FORMAT(r.cre_dat, '%d.%m.%Y um %H:%i') AS signdate,
				   r.public_remark,
				   r.private_remark,
				   r.registration_id,
				   s.student_id,
				   r.kid_name,
				   IF(s.membership = 1, levels.member_price, levels.price) AS price,
				   DATE_FORMAT(r.payment_reminder, '%d.%m.%Y') AS r_payment_reminder,
				   DATE_FORMAT(r.dunning, '%d.%m.%Y') AS r_dunning,
				   DATE_FORMAT(r.mail_waitlist, '%d.%m.%Y') AS r_mail_waitlist,
				   r.price_payed,
				   r.is_present1,
				   r.is_present2,
				   r.is_present3,
				   r.is_present4,
				   r.is_present5,
				   r.is_present6,
				   r.is_present7,
				   r.is_present8,
				   r.is_present9,
				   r.is_present10,
				   r.is_present11,
				   r.is_present12,
				    DATE_FORMAT(courses.date1, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date2, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date3, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date4, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date5, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date6, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date7, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date8, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date9, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date10, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date11, '%d.%m.%Y'),
				    DATE_FORMAT(courses.date12, '%d.%m.%Y'),
				    r.voucher
			  FROM 		as_registrations r
			 INNER JOIN as_students s       	ON r.student_id = s.student_id
			 INNER JOIN as_courses AS courses   ON courses.course_id = r.course_id
             INNER JOIN as_course_levels AS levels ON levels.id = courses.course_level_id
			 WHERE r.course_id = ?
			 ORDER BY r.rank")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($prename,
                $surname,
                $email,
                $rank,
                $status,
                $signdate,
                $public_remark,
                $private_remark,
                $registration_id,
                $student_id,
                $kid_name,
                $price,
                $r_payment_reminder,
                $r_dunning,
                $r_mail_waitlist,
                $r_price_payed,
                $r_is_present1,
                $r_is_present2,
                $r_is_present3,
                $r_is_present4,
                $r_is_present5,
                $r_is_present6,
                $r_is_present7,
                $r_is_present8,
                $r_is_present9,
                $r_is_present10,
                $r_is_present11,
                $r_is_present12,
                $c_date1,
                $c_date2,
                $c_date3,
                $c_date4,
                $c_date5,
                $c_date6,
                $c_date7,
                $c_date8,
                $c_date9,
                $c_date10,
                $c_date11,
                $c_date12,
                $r_voucher);

            echo "<div class='table-registrations'><table>
		         <tr><th>Vorname</th>
                 <th>Nachname</th>
                 <th>Email</th>"
                . (($is_kid_course == 1) ? '<th>Vorname<br/>des Kindes</th>' : '')
                . "<th>Preis</th>
                 <th>Bezahlt</th>
                 <th>Position</th>
                 <th>Anmeldestatus</th>
                 <th>Anmeldedatum</th>
                 <th>Anmeldungsnotizen</th>
                 <th>Anmeldungsnotizen</br>(verborgen)</th>
                 <th>Warteliste</th>
                 <th>Erinnerung</br></th>
                 <th>Mahnung</br></th>
                 <th>OS Block</br></th>
                 </tr>\n";


            $aggregated_active_emails = "";
            $aggregated_wait_list_emails = "";

            while ($pre_stmt->fetch()) {

                if ($status == '2' ||
                    $status == '3' ||
                    $status == '6' ||
                    $status == '7' ||
                    $status == '22' ||
                    $status == '23'
                ) {

                    $aggregated_active_emails .= $email . ";<br/>";
                }
                if ($status == '5') {

                    $aggregated_wait_list_emails .= $email . ";<br/>";
                }

                if ($p_edit_id == $registration_id) {
                    $status_select = "
							<select name='r_in_status' id='r-input-status' class='select-with-symbols'>
								<option value='1'" . (($status == 1) ? ' selected="selected" ' : '') . ">Vorgemerkt</option>
								<option value='2'" . (($status == 2) ? ' selected="selected" ' : '') . ">Angemeldet</option>
								<option value='3'" . (($status == 3) ? ' selected="selected" ' : '') . ">Bezahlt</option>
								<option value='80'" . (($status == 80) ? ' selected="selected" ' : '') . ">Bezahlt OS Block</option>
								<option value='4'" . (($status == 4) ? ' selected="selected" ' : '') . ">Vorgemerkt Warteliste</option>
								<option value='5'" . (($status == 5) ? ' selected="selected" ' : '') . ">Warteliste</option>
								<option value='6'" . (($status == 6) ? ' selected="selected" ' : '') . ">Nachholer</option>
								<option value='7'" . (($status == 7) ? ' selected="selected" ' : '') . ">Sonstiges</option>
								<option value='20'" . (($status == 20) ? ' selected="selected" ' : '') . ">Storno(abgelaufen)</option>
								<option value='21'" . (($status == 21) ? ' selected="selected" ' : '') . ">Abgemeldet</option>
								<option value='22'" . (($status == 22) ? ' selected="selected" ' : '') . ">Drop-In</option>
								<option value='23'" . (($status == 23) ? ' selected="selected" ' : '') . ">Stundenübernahme</option>
							</select>
							<div class='input-is-present'>";

                    if ($c_date1 != null) {
                        $status_select = $status_select . "<span>" . $c_date1 . "</span><input type='checkbox'  name='r_is_present1' value='1' " . ($r_is_present1 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date2 != null) {
                        $status_select = $status_select . "<span>" . $c_date2 . "</span><input type='checkbox'  name='r_is_present2' value='1' " . ($r_is_present2 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date3 != null) {
                        $status_select = $status_select . "<span>" . $c_date3 . "</span><input type='checkbox'  name='r_is_present3' value='1' " . ($r_is_present3 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date4 != null) {
                        $status_select = $status_select . "<span>" . $c_date4 . "</span><input type='checkbox'  name='r_is_present4' value='1' " . ($r_is_present4 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date5 != null) {
                        $status_select = $status_select . "<span>" . $c_date5 . "</span><input type='checkbox'  name='r_is_present5' value='1' " . ($r_is_present5 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date6 != null) {
                        $status_select = $status_select . "<span>" . $c_date6 . "</span><input type='checkbox'  name='r_is_present6' value='1' " . ($r_is_present6 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date7 != null) {
                        $status_select = $status_select . "<span>" . $c_date7 . "</span><input type='checkbox'  name='r_is_present7' value='1' " . ($r_is_present7 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date8 != null) {
                        $status_select = $status_select . "<span>" . $c_date8 . "</span><input type='checkbox'  name='r_is_present8' value='1' " . ($r_is_present8 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date9 != null) {
                        $status_select = $status_select . "<span>" . $c_date9 . "</span><input type='checkbox'  name='r_is_present9' value='1' " . ($r_is_present9 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date10 != null) {
                        $status_select = $status_select . "<span>" . $c_date10 . "</span><input type='checkbox'  name='r_is_present10' value='1' " . ($r_is_present10 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date11 != null) {
                        $status_select = $status_select . "<span>" . $c_date11 . "</span><input type='checkbox'  name='r_is_present11' value='1' " . ($r_is_present11 == '1' ? 'checked' : '') . "><br>";
                    }
                    if ($c_date12 != null) {
                        $status_select = $status_select . "<span>" . $c_date12 . "</span><input type='checkbox'  name='r_is_present12' value='1' " . ($r_is_present12 == '1' ? 'checked' : '') . "><br>";
                    }
                    $status_select = $status_select . "</div>";


                } else {
                    switch ($status) {
                        case 1:
                            $status = "<div style='color: orange; font-weight: bold'>Vorgemerkt</div>";
                            break;
                        case 2:
                            $status = "<div style='display:inline-block; color: #3399CC; font-weight: bold'>Angemeldet<div style= 'margin-left: 8px' pay_id='" . $registration_id . "' class='button-set-payed rb-mini-button'> ◀ </div></div>";
                            break;
                        case 3:
                            $status = "<div style='color: green; font-weight: bold'>Bezahlt</div>";
                            break;
                        case 4:
                            $status = "<div style='color: orange; font-weight: bold'>Vorg. Wartel.</div>";
                            break;
                        case 5:
                            $status = "<div style='color: #49004a; font-weight: bold'>Warteliste</div>";
                            break;
                        case 6:
                            $status = "<div style='color: green; font-weight: bold'>Nachholer</div>";
                            break;
                        case 7:
                            $status = "<div style='color: green; font-weight: bold'>Sonstiges</div>";
                            break;
                        case 20:
                            $status = "<div style='color: gray; font-weight: bold'>Storno(abgel.)</div>";
                            break;
                        case 21:
                            $status = "<div style='color: gray; font-weight: bold'>Abgemeldet</div>";
                            break;
                        case 22:
                            $status = "<div style='color: green; font-weight: bold'>Drop-In</div>";
                            break;
                        case 23:
                            $status = "<div style='color: green; font-weight: bold'>Stundenübernahme</div>";
                            break;
                        default:
                            $status = "?";
                            break;
                    }
                    $status_select = "error"; //$status;
                }


                echo "<tr registration_id='" . $registration_id . "'>";
                echo "<td>" . $prename . "</td>"
                    . "<td>" . $surname . "</td>"
                    . "<td class='email-text'>" . $email . "</td>"
                    . "<td class='price_student'>" . str_replace('.', ',', $price) . " €</td>"
                    . "<td class='r-price-payed'>"
                    . (($p_edit_id != $registration_id) ?
                        (isset($r_price_payed) ? str_replace('.', ',', $r_price_payed) : str_replace('.', ',', $price)) . " €"
                        : "<input name='r_price_payed' type='text' id='r_price_payed' value='" . (isset($r_price_payed) ? $r_price_payed : $price) . "'>") . "</td>";

                if ($is_kid_course == 1) {
                    echo "<td class='td-center'>" . (($p_edit_id == $registration_id) ? "<input name='r_in_kid_name' class='r-input-kid' type='text' value='$kid_name' required>" : $kid_name) . "</td>";
                } else {
                    echo ($p_edit_id == $registration_id) ? "<input type='hidden' name='r_in_kid_name' value='-'>" : "";
                }

                echo "<td class='td-center'>" . (($p_edit_id == $registration_id) ? "<input name='r_in_rank' class='r-input-rank' type='text' value='$rank' required>" : $rank) . "</td>"
                    . "<td class='td-center'>" . (($p_edit_id == $registration_id) ? $status_select : $status) . "</div></td>"
                    . "<td style='min-width: 150px'><div class='td-overflow'>" .
                    $signdate . "</div></td>"
                    . "<td style='min-width: 300px'><div class='td-overflow'>" .
                    (($p_edit_id == $registration_id) ? "<textarea name='r_in_public_remark' class='r-input-public-remark' cols='42' rows='7' >$public_remark</textarea>" : nl2br($public_remark)) . "</div></td>"
                    . "<td style='min-width: 300px'><div class='td-overflow'>" .
                    (($p_edit_id == $registration_id) ? "<textarea name='r_in_private_remark' class='r-input-private-remark' cols='42' rows='7' >$private_remark</textarea>" : nl2br($private_remark)) . "</div></td>";
//                    . "</tr>\n";
                echo "<td class='td-center'><div class='td-overflow '>" . $r_mail_waitlist . "</div></td>";

                echo "<td class='td-center'><div class='td-overflow r-payment-reminder'>" . (($p_edit_id == $registration_id)
                        ? ("<input name='r_payment_reminder' class='date-input' type='text' id='r_payment_reminder' placeholder='Datum' value='" . $r_payment_reminder . "'>")
                        : $r_payment_reminder) . "</div></td>";
                echo "<td class='td-center r-payment-reminder'><div class='td-overflow'>" . (($p_edit_id == $registration_id)
                        ? ("<input name='r_dunning' class='date-input' type='text' id='r_dunning' placeholder='Datum' value='" . $r_dunning . "'>")
                        : $r_dunning) . "</div></td>.
                       <td class='td-center'<div class='td-overflow'>" . (($r_voucher != null) ? $r_voucher : '') . " </div></td>

                      </tr>\n";
            }


            echo "</table></div>";

            if (empty($aggregated_wait_list_emails)) {
                $wait_list_text = "";
            } else {
                $wait_list_text = "<br/><br/>Warteliste:<br/><br/>" . $aggregated_wait_list_emails;
            }

            echo "<div title='E-Mail-Adressen' id='dialog-aggregated-emails' style='display:none'> E-Mail-Adressen ohne Vormerkung und Storno:<br/><br/>" . $aggregated_active_emails . $wait_list_text . "</div>";

            if ($pre_stmt->num_rows === 0) echo '</br>Derzeit sind für diesen Kurs noch keine Teilnehmer angemeldet.';

            $db->close();

        } else {
            echo "E-2034: Fehler: Laden der Anmeldungen fehlgeschlagen.";
            $db->close();
        }

    }

    public function db_sort_rank($p_course_id,
                                 $p_reg_id,
                                 $p_rank)

    {

        if (!(is_numeric($p_course_id) && is_numeric($p_reg_id) && is_numeric($p_rank))) return false;

        $db = $this->db_connect();
        $statement = "
			SELECT registration_id
			  FROM as_registrations
			 WHERE course_id = $p_course_id
			   AND registration_id != $p_reg_id
			 ORDER BY rank
			   FOR UPDATE;";

        $result = $db->query($statement);

        $result_array = $result->fetch_all(MYSQLI_ASSOC);

        $new_position = 1;
        foreach ($result_array as $row) {
            if ($new_position == $p_rank) {
                $new_position = $new_position + 1;
            }
            $db->query("UPDATE as_registrations
						   SET rank = " . $new_position . "
						 WHERE registration_id = " . $row['registration_id'] . ";");
            $new_position = $new_position + 1;

        }
        if ($p_rank > $new_position) {
            $db->query("UPDATE as_registrations
							   SET rank = $new_position
							 WHERE registration_id = $p_reg_id;");
        }
        $db->close();
    }

    public function db_get_registration_voucher($p_reg_id)
    {
        if (!isset($p_reg_id)) {
            $_SESSION["r_error_msg"] .= "Fehlerhafte Registrierungs Nummer";
            return false;
        }

        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		SELECT registration_id, student_id, status, voucher, course_id
         FROM as_registrations
         WHERE registration_id = ?;
        ")
        ) {
            $pre_stmt->bind_param("i", $p_reg_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($registration_id, $student_id, $status, $voucher, $course_id);
            $pre_stmt->fetch();
            $registration = ["registration_id" => $registration_id, "student_id" => $student_id, "status" => $status, "voucher" => $voucher, "course_id" => $course_id];
            $db->close();
            return $registration;

        } else {
            echo "E-2033: Fehler: Registrierung konnte nicht ermittelt werden.";
            return false;
        }
        $db->close();

    }

    public function db_is_voucher_course($p_course_id)
    {
        if (!isset($p_course_id)) {
            return false;
        }
        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("SELECT l.voucher FROM as_courses c
                                        INNER JOIN as_course_levels l
                                        ON c.course_level_id = l.id
                                        WHERE c.course_id  = ?")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($isVoucher);
            $pre_stmt->fetch();
            $pre_stmt->close();
            if ($isVoucher == null || $isVoucher == 0 || $isVoucher == "0") {
                return false;
            }
            return true;


        } else {
            echo "E-2033: Fehler: Kurs konnte nicht ermittelt werden.";
            return false;
        }
    }

    public function db_get_student_vouchers($p_student_id)
    {
        if (!isset($p_student_id)) {
            return false;
        }

        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("SELECT sum(amount) AS amount FROM voucher WHERE student = ? AND payed = 1 ;")
        ) {
            $pre_stmt->bind_param("i", $p_student_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($amountStudent);
            $pre_stmt->fetch();
            if ($amountStudent == null) {
                $amountStudent = 0;
            }
            $pre_stmt->close();
        } else {
            echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
            return false;
        }
        if ($pre_stmt = $db->prepare("SELECT count(registration_id) AS amountUsed FROM as_registrations WHERE voucher IS NOT NULL AND student_id = ?;")
        ) {
            $pre_stmt->bind_param("i", $p_student_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($amountUsed);
            $pre_stmt->fetch();
            if ($amountUsed == null) {
                $amountUsed = 0;
            }
            $pre_stmt->close();
            //Get CurrentOS Block
            if ($amountUsed < $amountStudent) {
                if ($pre_stmt = $db->prepare("SELECT amount FROM voucher WHERE student = ? AND payed = 1 ORDER BY added;")
                ) {
                    $pre_stmt->bind_param("i", $p_student_id);
                    $pre_stmt->execute();
                    $pre_stmt->bind_result($voucherAmount);
                    //Forward here
                    $calcVoucher = 0;
                    while ($pre_stmt->fetch()) {
                        $calcVoucher += $voucherAmount;
                        if($amountUsed < $calcVoucher){
                            $amountStudent = $voucherAmount;
                            if(($calcVoucher - $voucherAmount)  > 0){
                                $amountUsed = $amountUsed - ($calcVoucher - $voucherAmount);
                            }
                            $pre_stmt->close();

                            $db->close();
                            error_log("" . $amountStudent . " " . $amountUsed. " " . $voucherAmount);
                            $amount = ["amount_student" => $amountStudent, "amount_used" => $amountUsed];
                            return $amount;
                        }
                    }
                } else {
                    echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
                    return false;
                }
            } else {

                $db->close();
                $amount = ["amount_student" => $amountStudent, "amount_used" => $amountUsed];
                return $amount;
            }

        } else {
            echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
            return false;
        }
    }

    public function db_update_registration($p_reg_id,
                                           $p_course_id,
                                           $p_rank,
                                           $p_status,
                                           $p_public_remark,
                                           $p_private_remark,
                                           $p_kid_name,
                                           $r_payment_reminder,
                                           $r_dunning,
                                           $price_payed,
                                           $is_present1,
                                           $is_present2,
                                           $is_present3,
                                           $is_present4,
                                           $is_present5,
                                           $is_present6,
                                           $is_present7,
                                           $is_present8,
                                           $is_present9,
                                           $is_present10,
                                           $is_present11,
                                           $is_present12,
                                           $voucher
    )
    {
        // Validation
        $_SESSION["r_error_msg"] = "";
        if (!(!empty($p_rank) && is_numeric($p_rank))) {
            $_SESSION["r_error_msg"] .= "Bitte eine gültige Position angeben.<br>";
        }
        if (!(!empty($p_status) && is_numeric($p_status))) {
            $_SESSION["r_error_msg"] .= "Bitte einen gültigen Status wählen.<br>";
        }
        if (empty($p_kid_name)) {
            $_SESSION["r_error_msg"] .= "Bitte einen Kindervornamen angeben.<br>";
        } else {
            $p_kid_name = "'" . $p_kid_name . "'";
        }
        $p_public_remark = "'" . $p_public_remark . "'";
        $p_private_remark = "'" . $p_private_remark . "'";

        if ($p_status == 20 || $p_status == 21) {
            $p_rank = 99999;
        }

        //Check Dates
        $save_payment_reminder = $r_payment_reminder ? "STR_TO_DATE('$r_payment_reminder','%d.%m.%Y')" : "NULL";
        $save_dunning = $r_dunning ? "STR_TO_DATE('$r_dunning','%d.%m.%Y')" : "NULL";

        $db = $this->db_connect();

        $statement = "
			UPDATE as_registrations
			   SET	   rank = $p_rank,
					   status = $p_status,
					   public_remark = $p_public_remark,
					   private_remark = $p_private_remark,
					   kid_name = $p_kid_name,
					   mod_dat = now(),
					   payment_reminder = " . $save_payment_reminder . ",
					   dunning =  " . $save_dunning . ",
					   price_payed = " . $price_payed . ",
					   is_present1 = " . $is_present1 . ",
					   is_present2 = " . $is_present2 . ",
					   is_present3 = " . $is_present3 . ",
					   is_present4 = " . $is_present4 . ",
					   is_present5 = " . $is_present5 . ",
					   is_present6 = " . $is_present6 . ",
					   is_present7 = " . $is_present7 . ",
					   is_present8 = " . $is_present8 . ",
					   is_present9 = " . $is_present9 . ",
					   is_present10 = " . $is_present10 . ",
					   is_present11 = " . $is_present11 . ",
					   is_present12 = " . $is_present12 . " ,
					   voucher = " . ($voucher != null ? " '$voucher'" : "null") . "
			WHERE registration_id= $p_reg_id;";


        $result = false;
        $_SESSION["r_success_msg"] = false;
        if (empty($_SESSION["r_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["r_error_msg"] = $db->error . "<br><br>" . $statement;
                $db->close();
                return false;
            } else {

                $_SESSION["r_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $this->db_sort_rank($p_course_id, $p_reg_id, $p_rank);

                global $db_functions;
                $db_functions->courses->db_update_actual_count($p_course_id);

                $db->close();
                return true;
            }
        }

    }


    public function db_set_to_paid($p_reg_id)

    {
        $db = $this->db_connect();

        $statement = "
			UPDATE as_registrations
			   SET	   status = 3,
					   mod_dat = now()
			WHERE registration_id= $p_reg_id
			  AND status = 2";

        $result = $db->query($statement);
        if (!$result) {
            $_SESSION["r_error_msg"] = $db->error . "<br><br>" . $statement;
            $db->close();
            return false;
        } else {
            $_SESSION["r_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
            $db->close();
            return true;
        }
    }

    public function db_set_payment_reminder($p_reg_id)
    {
        $db = $this->db_connect();

        $statement = "
			UPDATE as_registrations
			   SET payment_reminder = now()
			WHERE registration_id= $p_reg_id";

        $result = $db->query($statement);
        if (!$result) {
            $_SESSION["r_error_msg"] = $db->error . "<br><br>" . $statement;
            $db->close();
            return false;
        } else {
            $_SESSION["r_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
            $db->close();
            return true;
        }
    }

    public function db_set_dunning($p_reg_id)
    {
        $db = $this->db_connect();

        $statement = "
			UPDATE as_registrations
			   SET dunning = now()
			WHERE registration_id= $p_reg_id";

        $result = $db->query($statement);
        if (!$result) {
            $_SESSION["r_error_msg"] = $db->error . "<br><br>" . $statement;
            $db->close();
            return false;
        } else {
            $_SESSION["r_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
            $db->close();
            return true;
        }
    }

    public function db_set_waitlist($p_reg_id)
    {
        $db = $this->db_connect();

        $statement = "
			UPDATE as_registrations
			   SET mail_waitlist = now()
			WHERE registration_id= $p_reg_id";

        $result = $db->query($statement);
        if (!$result) {
            $_SESSION["r_error_msg"] = $db->error . "<br><br>" . $statement;
            $db->close();
            return false;
        } else {
            $_SESSION["r_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
            $db->close();
            return true;
        }
    }


    public function db_update_preregistrations()  // Status vorgemerkt updaten

    {


        $db = $this->db_connect();
        $statement = "
			UPDATE as_registrations
			   SET status = 20,
			   	   re_calc_course = 1
			 WHERE status IN (1,4)
			   AND mod_dat + INTERVAL 5 HOUR < now();";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";

        $statement = "
			UPDATE as_courses c
			 INNER JOIN as_registrations r ON c.course_id = r.course_id
			   SET c.pre_reg_count	  = (SELECT count(1) FROM as_registrations r2
					   							WHERE r2.course_id = c.course_id
												  AND r2.status IN (1,4,5))
			 WHERE r.re_calc_course = 1;";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";

        $statement = "
			UPDATE as_registrations
			   SET re_calc_course = 0
			 WHERE re_calc_course = 1;";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";

        $db->close();
    }


}


class DB_Functions_Courses_Extras extends DB_Connect
{

    public function db_load_table_attendance($p_course_id, $p_dates)
    {


        global $db_functions;
        $is_kid_course = $db_functions->courses->db_check_if_kid_course($p_course_id);

        $db = $this->db_connect();


        if ($pre_stmt = $db->prepare("
		
			SELECT s.prename,
				   s.surname,
				   s.email,
				   r.rank,
				   r.status,
				   r.public_remark,
				   r.private_remark,
				   r.registration_id,
				   r.kid_name,
				   s.student_id,
				   r.present1,
				   r.present2,
				   r.present3,
				   r.present4,
				   r.present5,
				   r.present6,
				   r.present7,
				   r.present8,
				   r.present9,
				   r.present10,
				   r.present11,
				   r.present12,
				   r.is_present1,
				   r.is_present2,
				   r.is_present3,
				   r.is_present4,
				   r.is_present5,
				   r.is_present6,
				   r.is_present7,
				   r.is_present8,
				   r.is_present9,
				   r.is_present10,
				   r.is_present11,
				   r.is_present12,
				   r.voucher
			  FROM 		as_registrations r
			 INNER JOIN as_students s       ON r.student_id = s.student_id
			 WHERE r.course_id = ?
			   AND r.status IN (2,3,6,7,22,23)" .  // Status angemeldet, bestätigt, Nachholer, Sonstiges, Drop-In, Stundenübernahme
            "ORDER BY r.rank")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($prename,
                $surname,
                $email,
                $rank,
                $status,
                $public_remark,
                $private_remark,
                $registration_id,
                $kid_name,
                $student_id,
                $present1,
                $present2,
                $present3,
                $present4,
                $present5,
                $present6,
                $present7,
                $present8,
                $present9,
                $present10,
                $present11,
                $present12,
                $r_is_present1,
                $r_is_present2,
                $r_is_present3,
                $r_is_present4,
                $r_is_present5,
                $r_is_present6,
                $r_is_present7,
                $r_is_present8,
                $r_is_present9,
                $r_is_present10,
                $r_is_present11,
                $r_is_present12,
                $r_voucher
            );

            echo "\n<div class='table-attendance'><table>\n";
            echo "<tr><th>Termin-Nr.</br>Name</th>";
            for ($i = 1; $i <= 12; $i++) {
                if (empty($p_dates[$i - 1])) {
                    $date_count = $i - 1;
                    break;
                }
                if ($i == 12) $date_count = 12;
            }
            if ($date_count <= 6) {
                $width_extended = "-extended";
            } else {
                $width_extended = "";
            }
            echo "<th>Zahl-<br />status</h>";
            $date_header = "";
            for ($i = 1; $i <= 12; $i++) {
                if (!empty($p_dates[$i - 1])) $date_header .= "<th>" . $i . "</br>" . $p_dates[$i - 1] . "</th>";
            }

            echo $date_header;

            echo "</tr>\n";
            $k = 1;
            global $rb_functions;
            while ($pre_stmt->fetch()) {

                switch ($status) {
                    case 3:
                        $status = "<div " . $rb_functions->ignore_android("style='color: green'") . ">✔</div>";
                        break;
                    case 6:
                    case 7:
                    case 22:
                    case 23:
                        $status = "<div style='font-weight: normal; font-size: 15px; " . $rb_functions->ignore_android("color: green;") . "'>Nach-<br />holer</div>"; // * Nachholer, Sonstiges, Drop-In, Stundenübernahme
                        break;
                    default:
                        $status = "<div " . $rb_functions->ignore_android("style='color: red'") . ">✖</div>";
                        break;
                }
                if ($r_voucher != null) {
                    $status .= ("<div style='font-size: medium;'>" . $r_voucher . "</div>");
                }

                echo "<tr registration_id='" . $registration_id . "'>\n";
                if ($is_kid_course == 1) {

                    echo "<td>" . $kid_name . "<br/>(" . $prename . "</br>" . $surname . ")</td>\n";
                } else {
                    echo "<td>" . $prename . "</br>" . $surname . "</td>\n";
                }
                echo "<td class='td-center' style='font-size: 20px; font-weight: bold;'>" . $status . "</div></td>";


                $date_lines = "";
                for ($i = 1; $i <= 12; $i++) {
                    $present = "present" . $i;
                    $is_present = "r_is_present" . $i;
                    if (empty($$present)) {
                        $checked = "";
                    } else {
                        $checked = "checked";
                    }
                    if (!empty($p_dates[$i - 1])) {
                        if ($$is_present == '1') {
                            $date_lines .= "<td><div class='rb-checkbox" . $width_extended . "'><input type='checkbox' id='attendance-checkbox" . $k . "-" . $i . "'name='a_attendance" . $k . "-" . $i . "' " . $checked . "></div></td>\n";
                        } else {
                            $date_lines .= "<td></td>";
                        }
                    }
                }
                echo "<input type='hidden' name='a_registration_id" . $k . "' value='" . $registration_id . "'>\n";
                echo $date_lines;
                echo "</tr>\n";
                $k++;
            }

            echo "</table></div>";
            if ($pre_stmt->num_rows === 0) echo '</br>Derzeit sind für diesen Kurs noch keine Teilnehmer angemeldet.';

            $db->close();

        } else {
            echo "E-2034: Fehler: Laden der Checkliste fehlgeschlagen.";
            $db->close();
        }

    }

    public function db_load_table_trainer_payment()
    {


        global $db_functions;

        $db = $this->db_connect();


        if ($pre_stmt = $db->prepare("
		SELECT course_id, as_courses.name, as_course_formats.name,
		 as_course_types.name, as_course_levels.name, as_users.prename,
		  as_locations.location_name, 
		  DATE_FORMAT(date1, '%d.%m.%Y um %H:%i') AS date1, paid1,
		  DATE_FORMAT(date2, '%d.%m.%Y um %H:%i') AS date2, paid2,
		  DATE_FORMAT(date3, '%d.%m.%Y um %H:%i') AS date3, paid3,
		  DATE_FORMAT(date4, '%d.%m.%Y um %H:%i') AS date4, paid4,
		  DATE_FORMAT(date5, '%d.%m.%Y um %H:%i') AS date5, paid5,
		  DATE_FORMAT(date6, '%d.%m.%Y um %H:%i') AS date6, paid6,
		  DATE_FORMAT(date7, '%d.%m.%Y um %H:%i') AS date7, paid7,
		  DATE_FORMAT(date8, '%d.%m.%Y um %H:%i') AS date8, paid8,
		  DATE_FORMAT(date9, '%d.%m.%Y um %H:%i') AS date9, paid9,
		  DATE_FORMAT(date10, '%d.%m.%Y um %H:%i') AS date10, paid10,
		  DATE_FORMAT(date11, '%d.%m.%Y um %H:%i') AS date11, paid11,
		  DATE_FORMAT(date12, '%d.%m.%Y um %H:%i') AS date12, paid12
		  FROM as_courses
        JOIN as_users ON as_users.user_id = as_courses.trainer_id
        JOIN as_locations ON as_courses.location_id = as_locations.location_id
        JOIN as_course_formats ON as_course_formats.id = as_courses.course_format_id
        JOIN as_course_types ON as_course_types.id = as_courses.course_type_id
        JOIN as_course_levels ON as_course_levels.id = as_courses.course_level_id
        WHERE date1 IS NOT NULL
        ORDER BY date1 DESC;")
        ) {
            $pre_stmt->execute();
            $pre_stmt->bind_result($course_id, $course_name, $course_format, $course_type, $course_level, $trainer, $location,
                $date1, $paid1,
                $date2, $paid2,
                $date3, $paid3,
                $date4, $paid4,
                $date5, $paid5,
                $date6, $paid6,
                $date7, $paid7,
                $date8, $paid8,
                $date9, $paid9,
                $date10, $paid10,
                $date11, $paid11,
                $date12, $paid12);

            echo "<div class='table-attendance'><table>";
            echo "<tr>";
            echo "<th>Kurs-Nr.</h>";
            echo "<th>Kursblockinfo</th>";
            echo "<th>Trainer</th>";
            echo "<th>Termin 1</th>";
            echo "<th>Termin 2</th>";
            echo "<th>Termin 3</th>";
            echo "<th>Termin 4</th>";
            echo "<th>Termin 5</th>";
            echo "<th>Termin 6</th>";
            echo "<th>Termin 7</th>";
            echo "<th>Termin 8</th>";
            echo "<th>Termin 9</th>";
            echo "<th>Termin 10</th>";
            echo "<th>Termin 11</th>";
            echo "<th>Termin 12</th>";
            echo "</tr>";
            global $rb_functions;

            while ($pre_stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $course_id . "</td>";
                echo "<td>" . $course_name . "<br/>" . $course_format . "<br/>" . "<br/>" . $course_type . "<br/>" . $course_level . "<br/>" . $location . "</td>";
                echo "<td>" . $trainer . "</td>";
                if (isset($date1)) echo "<td>" . $date1 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid1' name='" . $course_id . "-paid1' " . ($paid1 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date2)) echo "<td>" . $date2 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid2' name='" . $course_id . "-paid2' " . ($paid2 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date3)) echo "<td>" . $date3 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid3' name='" . $course_id . "-paid3' " . ($paid3 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date4)) echo "<td>" . $date4 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid4' name='" . $course_id . "-paid4' " . ($paid4 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date5)) echo "<td>" . $date5 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid5' name='" . $course_id . "-paid5' " . ($paid5 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date6)) echo "<td>" . $date6 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid6' name='" . $course_id . "-paid6' " . ($paid6 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date7)) echo "<td>" . $date7 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid7' name='" . $course_id . "-paid7' " . ($paid7 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date8)) echo "<td>" . $date8 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid8' name='" . $course_id . "-paid8' " . ($paid8 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date9)) echo "<td>" . $date9 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid9' name='" . $course_id . "-paid9' " . ($paid9 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date10)) echo "<td>" . $date10 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid10' name='" . $course_id . "-paid10' " . ($paid10 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date11)) echo "<td>" . $date11 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid11' name='" . $course_id . "-paid11' " . ($paid11 != '' ? 'checked' : '') . "></div>" . "</td>";
                if (isset($date12)) echo "<td>" . $date12 . "<br/> <div class='rb-checkbox'><input type='checkbox' class='date-checkbox' id='paid12' name='" . $course_id . "-paid12' " . ($paid12 != '' ? 'checked' : '') . "></div>" . "</td>";
                echo "</tr>";

            }

            echo "</table></div>";
            $db->close();

        } else {
            echo "E-2034: Fehler: Laden der Trainerabrechnung fehlgeschlagen.";
            $db->close();
        }

    }

    public function db_update_trainer_payment($p_trainer_payment)
    {
        if (!isset($p_trainer_payment)) return false;

        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("UPDATE as_courses SET paid1 = NULL, paid2 = NULL, paid3 = NULL, paid4 = NULL, paid5 = NULL, paid6 = NULL, paid7 = NULL, paid8 = NULL, paid9 = NULL, paid10 = NULL, paid11 = NULL, paid12 = NULL;")) {
            $pre_stmt->execute();
            foreach ($p_trainer_payment as $key => $value) {
                $queryString = "UPDATE as_courses SET";
                foreach ($value as $field) {
                    $queryString = $queryString . " " . $field . " = IF(" . $field . " IS NULL, NOW(), " . $field . ")" . " ,";
                }
                $queryString = rtrim($queryString, ", ");
                $queryString = $queryString . " WHERE course_id = " . $key . ";";
                if ($pre_stmt = $db->prepare($queryString)) {
                    $pre_stmt->execute();
                } else {
                    echo $db->error;
                    echo "E-2033: Fehler: update failed";
                    $db->close();
                    return false;
                }
            }
            $db->close();
            return true;
        }
        return false;
    }

    /*
	public function db_load_course_detail_in_one_column_disabled($p_id){


			$db=$this->db_connect();
			$result = $db->query(

				"SELECT
						c.course_id,
						c.status,
						c.name as kursname,
						u.prename as trainer,
						l.location_name as ort,
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
							)	as begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')

							)	as time,
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
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.'))

							) as termine,
						Concat
							(
								c.actual_count,
								' / ',
								c.max_count
							) as anmeldungen,
						replace(format(c.price, 2), '.00', '' ) as price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code,
						if(c.date1 IS NULL, NULL, date_format(c.date1, '%d.%m.')) as date1,
						if(c.date2 IS NULL, NULL, date_format(c.date2, '%d.%m.')) as date2,
						if(c.date3 IS NULL, NULL, date_format(c.date3, '%d.%m.')) as date3,
						if(c.date4 IS NULL, NULL, date_format(c.date4, '%d.%m.')) as date4,
						if(c.date5 IS NULL, NULL, date_format(c.date5, '%d.%m.')) as date5,
						if(c.date6 IS NULL, NULL, date_format(c.date6, '%d.%m.')) as date6,
						if(c.date7 IS NULL, NULL, date_format(c.date7, '%d.%m.')) as date7,
						if(c.date8 IS NULL, NULL, date_format(c.date8, '%d.%m.')) as date8,
						if(c.date9 IS NULL, NULL, date_format(c.date9, '%d.%m.')) as date9,
						if(c.date10 IS NULL, NULL, date_format(c.date10, '%d.%m.')) as date10,
						check1,
						check2,
						check3,
						check4,
						check5,
						check6,
						check7,
						check8,
						check9,
						check10
				   from
							  as_courses c
				   inner join as_users u			on c.trainer_id = u.user_id
				   inner join as_locations l		on c.location_id = l.location_id
				   left  join as_categories ca		on c.cat_id = ca.cat_id
				   where c.course_id = " . $p_id . "
					  and     (" . $_SESSION['user_is_organizer'] . " = 1
							or (c.trainer_id = " . $_SESSION['user_id'] . "
								or c.trainer_id2 = " . $_SESSION['user_id'] . ")
				  			or " . $_SESSION['user_is_admin'] . " = 1);");

			if(!$result) echo $db->error;

			$load_date = true;
			$dates = null;
			while($line = $result->fetch_array()) {


				 $dates	= array($line["date1"],
								$line["date2"],
								$line["date3"],
								$line["date4"],
								$line["date5"],
								$line["date6"],
								$line["date7"],
								$line["date8"],
								$line["date9"],
								$line["date10"]);
				$load_date = false;

				$_SESSION["safety_check1"] = $line["check1"];
				$_SESSION["safety_check2"] = $line["check2"];
				$_SESSION["safety_check3"] = $line["check3"];
				$_SESSION["safety_check4"] = $line["check4"];
				$_SESSION["safety_check5"] = $line["check5"];
				$_SESSION["safety_check6"] = $line["check6"];
				$_SESSION["safety_check7"] = $line["check7"];
				$_SESSION["safety_check8"] = $line["check8"];
				$_SESSION["safety_check9"] = $line["check9"];
				$_SESSION["safety_check10"] = $line["check10"];

				$_SESSION["r_registration_code"] = $line["registration_code"];

				?>
				<ul>
					<li><b>Kurs- Nr.: <?=$line['course_id']?></b></li>
					<li>Kursname: <?=$line['kursname']?></li>
					<li>Beginn: <?=$line['begin1']?></li>
					<li>Uhrzeit: <?=$line['time']?></li>
					<li>Trainer: <?=$line['trainer']?></li>
					<li>Termine: <?=$line['termine']?></li>
					<li>Ort: <?=$line['ort']?></li>
					<li>Preis: <?=$line['price']?> €</li>
					<li>Anmeldungen: <?=$line['anmeldungen']?></li>
				</ul>

				<?

			}
			if($result->num_rows === 0) {
				if($_SESSION["user_is_organizer"] == "1" ||
		  		   $_SESSION["user_is_admin"] == "1") {
		  		   		echo '<br>Keinen Kurs gefunden.';
		  		   }else{
		  		   		echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " .  $_SESSION['user_surname'] . " gefunden.";
		  		   }
				$db->close();
				return array('date1' => NULL);


			}else {
				$db->close();
				return $dates;
			}

			$db->close();
			return array('date1' => NULL);

	} */


    public function db_load_course_detail($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
						c.course_id,
						c.status,
						c.name AS kursname,
						Concat
							(
							u.prename,
							if(u2.prename IS NULL, '', Concat(', ', u2.prename))
							)
						AS trainer,
						l.location_name AS ort,
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
							)	AS begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	AS time,
						IF(
                                (c.date2 IS NULL OR date_format(c.date2, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date3 IS NULL OR date_format(c.date3, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date4 IS NULL OR date_format(c.date4, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date5 IS NULL OR date_format(c.date5, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date6 IS NULL OR date_format(c.date6, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date7 IS NULL OR date_format(c.date7, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date8 IS NULL OR date_format(c.date8, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date9 IS NULL OR date_format(c.date9, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date10 IS NULL OR date_format(c.date10, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date11 IS NULL OR date_format(c.date11, '%H:%i') = date_format(c.begin, '%H:%i')) AND
                                (c.date12 IS NULL OR date_format(c.date12, '%H:%i') = date_format(c.begin, '%H:%i'))
                                ,Concat(
                                    date_format(c.date1, '%d.%m'),
                                    if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
                                    if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
                                    if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
                                    if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
                                    if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
                                    if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
                                    if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
                                    if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
                                    if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
                                    if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
                                    if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
                                    if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')'))
                                )
                                ,Concat (
                                    date_format(c.date1, '%d.%m %H:%i'),
                                    if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m %H:%i')),
                                    if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m %H:%i')),
                                    if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m %H:%i')),
                                    if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m %H:%i')),
                                    if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m %H:%i')),
                                    if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m %H:%i')),
                                    if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m %H:%i')),
                                    if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m %H:%i')),
                                    if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m %H:%i')),
                                    if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m %H:%i')),
                                    if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m %H:%i')),
                                    if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')')),
                                    '.')
                                ) AS termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) AS anmeldungen0,
						c.pre_reg_count AS voranmeldungen,
						c.registration_count AS anmeldungen,
						c.max_count	AS kursplatzanzahl,
						replace(format(c.price, 2), '.00', '' ) AS price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code,
						if(c.date1 IS NULL, NULL, date_format(c.date1, '%d.%m.')) AS date1,
						if(c.date2 IS NULL, NULL, date_format(c.date2, '%d.%m.')) AS date2,
						if(c.date3 IS NULL, NULL, date_format(c.date3, '%d.%m.')) AS date3,
						if(c.date4 IS NULL, NULL, date_format(c.date4, '%d.%m.')) AS date4,
						if(c.date5 IS NULL, NULL, date_format(c.date5, '%d.%m.')) AS date5,
						if(c.date6 IS NULL, NULL, date_format(c.date6, '%d.%m.')) AS date6,
						if(c.date7 IS NULL, NULL, date_format(c.date7, '%d.%m.')) AS date7,
						if(c.date8 IS NULL, NULL, date_format(c.date8, '%d.%m.')) AS date8,
						if(c.date9 IS NULL, NULL, date_format(c.date9, '%d.%m.')) AS date9,
						if(c.date10 IS NULL, NULL, date_format(c.date10, '%d.%m.')) AS date10,
						if(c.date11 IS NULL, NULL, date_format(c.date10, '%d.%m.')) AS date11,
						if(c.date12 IS NULL, NULL, date_format(c.date10, '%d.%m.')) AS date12,
						check1,
						check2,
						check3,
						check4,
						check5,
						check6,
						check7,
						check8,
						check9,
						check10,
						check11,
						check12,
						ca.title AS category,
						ca.only_cash_allowed,
						c.todo,
						c.precondition,
						c.textblock_mode,
						c.textblock
				   FROM
							  as_courses c
				   INNER JOIN as_users u			ON c.trainer_id = u.user_id
				   INNER JOIN as_locations l		ON c.location_id = l.location_id
				   LEFT  JOIN as_categories ca		ON c.cat_id = ca.cat_id
				   LEFT  JOIN as_users u2			ON c.trainer_id2 = u2.user_id
				   WHERE c.course_id = " . $p_id . "
					  AND     (" . $_SESSION['user_is_organizer'] . " = 1
							OR (c.trainer_id = " . $_SESSION['user_id'] . "
								OR c.trainer_id2 = " . $_SESSION['user_id'] . ")
				  			OR " . $_SESSION['user_is_admin'] . " = 1);");

        if (!$result) echo $db->error;

        $load_date = true;
        $dates = null;
        if ($line = $result->fetch_array()) {


            $dates = array($line["date1"],
                $line["date2"],
                $line["date3"],
                $line["date4"],
                $line["date5"],
                $line["date6"],
                $line["date7"],
                $line["date8"],
                $line["date9"],
                $line["date10"],
                $line["date11"],
                $line["date12"]);
            $load_date = false;

            $_SESSION["safety_check1"] = $line["check1"];
            $_SESSION["safety_check2"] = $line["check2"];
            $_SESSION["safety_check3"] = $line["check3"];
            $_SESSION["safety_check4"] = $line["check4"];
            $_SESSION["safety_check5"] = $line["check5"];
            $_SESSION["safety_check6"] = $line["check6"];
            $_SESSION["safety_check7"] = $line["check7"];
            $_SESSION["safety_check8"] = $line["check8"];
            $_SESSION["safety_check9"] = $line["check9"];
            $_SESSION["safety_check10"] = $line["check10"];
            $_SESSION["safety_check11"] = $line["check11"];
            $_SESSION["safety_check12"] = $line["check12"];
            $_SESSION["r_registration_code"] = $line["registration_code"];

            $_SESSION["todo"] = $line["todo"];

            global $rb_functions;

            switch ($line["status"]) {
                case 0:  // deaktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: red'") . ">✖</span>";
                    break;
                case 1:  // in Bearbeitung
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: #E69400'") . ">✎</span>";
                    break;
                case 2:  // aktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔</span>";
                    break;
                case 3:  // aktiviert, alle Teilnehmer haben bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔</span>";
                    break;
                case 4: // aktiviert, Teilnehmer und Trainer bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔✔</span>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }

            ?>
            <div id='course-detail-container' class="clearfix">
                <ul style="margin-bottom: 0px;">
                    <li><b>Kurs- Nr.: <?= $line['course_id'] ?></b></li>
                    <li><?= $line['kursname'] ?></li>
                    <li>Beginn: <?= $line['begin1'] ?></li>
                    <li>Uhrzeit: <?= $line['time'] ?></li>
                    <li>Trainer: <?= $line['trainer'] ?></li>
                    <li>Termine: <?= $line['termine'] ?></li>
                    <li>Kursbeitrag: <?= $line['price'] ?> €</li>
                    <? if (!empty($line['precondition'])) { ?>
                        <li>Voraussetzungen: <?= $line['precondition'] ?></li>
                        <?
                    } ?>

                    <li>Kategorie: <?= $line['category'] ?></li>
                    <li>Ort: <?= $line['ort'] ?></li>
                    <li>Voranmeldungen: <?= $line['voranmeldungen'] ?></li>
                    <li>Anmeldungen: <?= $line['anmeldungen'] ?> / <?= $line['kursplatzanzahl'] ?></li>
                    <li>Status: <?= $line['status'] ?></li>

                </ul>
                <? if ($line['textblock_mode'] == 1) {
                    echo nl2br("<ul style='margin-bottom: 0px;'><li>" . $line['textblock'] . "</li></ul>");
                } ?>
            </div>
            <br/>

            <?

        }
        if ($result->num_rows === 0) {
            if ($_SESSION["user_is_organizer"] == "1" ||
                $_SESSION["user_is_admin"] == "1"
            ) {
                echo '<br>Keinen Kurs gefunden.';
            } else {
                echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " . $_SESSION['user_surname'] . " gefunden.";
            }
            $db->close();
            return array('date1' => NULL);


        } else {
            $db->close();
            return $dates;
        }

        $db->close();
        return array('date1' => NULL);

    }

    public function db_load_course_detail_old3($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
						c.course_id,
						c.status,
						c.name AS kursname,
						Concat
							(
							u.prename,
							if(u2.prename IS NULL, '', Concat(', ', u2.prename))
							)
						AS trainer,
						l.location_name AS ort,
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
							)	AS begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	AS time,
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
								if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ' )'))
							
							) AS termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) AS anmeldungen,
						c.pre_reg_count AS voranmeldungen,
						c.registration_count AS anmeldungen,
						c.max_count	AS kursplatzanzahl,
						replace(format(c.price, 2), '.00', '' ) AS price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code,
						if(c.date1 IS NULL, NULL, date_format(c.date1, '%d.%m.')) AS date1,
						if(c.date2 IS NULL, NULL, date_format(c.date2, '%d.%m.')) AS date2,
						if(c.date3 IS NULL, NULL, date_format(c.date3, '%d.%m.')) AS date3,
						if(c.date4 IS NULL, NULL, date_format(c.date4, '%d.%m.')) AS date4,
						if(c.date5 IS NULL, NULL, date_format(c.date5, '%d.%m.')) AS date5,
						if(c.date6 IS NULL, NULL, date_format(c.date6, '%d.%m.')) AS date6,
						if(c.date7 IS NULL, NULL, date_format(c.date7, '%d.%m.')) AS date7,
						if(c.date8 IS NULL, NULL, date_format(c.date8, '%d.%m.')) AS date8,
						if(c.date9 IS NULL, NULL, date_format(c.date9, '%d.%m.')) AS date9,
						if(c.date10 IS NULL, NULL, date_format(c.date10, '%d.%m.')) AS date10,
						check1,
						check2,
						check3,
						check4,
						check5,
						check6,
						check7,
						check8,
						check9,
						check10,
						ca.title AS category,
						c.todo
				   FROM
							  as_courses c
				   INNER JOIN as_users u			ON c.trainer_id = u.user_id
				   INNER JOIN as_locations l		ON c.location_id = l.location_id
				   LEFT  JOIN as_categories ca		ON c.cat_id = ca.cat_id
				   LEFT  JOIN as_users u2			ON c.trainer_id2 = u2.user_id
				   WHERE c.course_id = " . $p_id . "
					  AND     (" . $_SESSION['user_is_organizer'] . " = 1
							OR (c.trainer_id = " . $_SESSION['user_id'] . "
								OR c.trainer_id2 = " . $_SESSION['user_id'] . ")
				  			OR " . $_SESSION['user_is_admin'] . " = 1);");

        if (!$result) echo $db->error;

        $load_date = true;
        $dates = null;
        if ($line = $result->fetch_array()) {


            $dates = array($line["date1"],
                $line["date2"],
                $line["date3"],
                $line["date4"],
                $line["date5"],
                $line["date6"],
                $line["date7"],
                $line["date8"],
                $line["date9"],
                $line["date10"]);
            $load_date = false;

            $_SESSION["safety_check1"] = $line["check1"];
            $_SESSION["safety_check2"] = $line["check2"];
            $_SESSION["safety_check3"] = $line["check3"];
            $_SESSION["safety_check4"] = $line["check4"];
            $_SESSION["safety_check5"] = $line["check5"];
            $_SESSION["safety_check6"] = $line["check6"];
            $_SESSION["safety_check7"] = $line["check7"];
            $_SESSION["safety_check8"] = $line["check8"];
            $_SESSION["safety_check9"] = $line["check9"];
            $_SESSION["safety_check10"] = $line["check10"];
            $_SESSION["r_registration_code"] = $line["registration_code"];

            $_SESSION["todo"] = $line["todo"];

            global $rb_functions;

            switch ($line["status"]) {
                case 0:  // deaktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: red'") . ">✖</span>";
                    break;
                case 1:  // in Bearbeitung
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: #E69400'") . ">✎</span>";
                    break;
                case 2:  // aktiviert
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔</span>";
                    break;
                case 3:  // aktiviert, alle Teilnehmer haben bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔</span>";
                    break;
                case 4: // aktiviert, Teilnehmer und Trainer bezahlt
                    $line["status"] = "<span " . $rb_functions->ignore_android("style='color: green'") . ">✔✔✔</span>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }

            ?>
            <div id='course-detail-container-in-two-columns'>
                <ul>
                    <li><b>Kurs- Nr.: <?= $line['course_id'] ?></b></li>
                    <li><?= $line['kursname'] ?></li>
                    <li>Beginn: <?= $line['begin1'] ?></li>
                    <li>Uhrzeit: <?= $line['time'] ?></li>
                    <li>Trainer: <?= $line['trainer'] ?></li>
                    <li>Termine: <?= $line['termine'] ?></li>
                </ul>
                <ul>
                    <li>Kategorie: <?= $line['category'] ?></li>
                    <li>Ort: <?= $line['ort'] ?></li>
                    <li>Preis: <?= $line['price'] ?> €</li>
                    <li>Voranmeldungen: <?= $line['voranmeldungen'] ?></li>
                    <li>Anmeldungen: <?= $line['anmeldungen'] ?> / <?= $line['kursplatzanzahl'] ?></li>
                    <?//<li>Kursplätze: <?=$line['kursplatzanzahl']? ></li>?>
                    <li>Status: <?= $line['status'] ?></li>
                </ul>
            </div>

            <?

        }
        if ($result->num_rows === 0) {
            if ($_SESSION["user_is_organizer"] == "1" ||
                $_SESSION["user_is_admin"] == "1"
            ) {
                echo '<br>Keinen Kurs gefunden.';
            } else {
                echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " . $_SESSION['user_surname'] . " gefunden.";
            }
            $db->close();
            return array('date1' => NULL);


        } else {
            $db->close();
            return $dates;
        }

        $db->close();
        return array('date1' => NULL);

    }

    /*
	public function db_load_course_detail_in_two_column_disabled($p_id){


			$db=$this->db_connect();
			$result = $db->query(

				"SELECT
						c.course_id,
						c.status,
						c.name as kursname,
						u.prename as trainer,
						l.location_name as ort,
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
							)	as begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')

							)	as time,
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
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.'))

							) as termine,
						Concat
							(
								c.actual_count,
								' / ',
								c.max_count
							) as anmeldungen,
						replace(format(c.price, 2), '.00', '' ) as price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code,
						if(c.date1 IS NULL, NULL, date_format(c.date1, '%d.%m.')) as date1,
						if(c.date2 IS NULL, NULL, date_format(c.date2, '%d.%m.')) as date2,
						if(c.date3 IS NULL, NULL, date_format(c.date3, '%d.%m.')) as date3,
						if(c.date4 IS NULL, NULL, date_format(c.date4, '%d.%m.')) as date4,
						if(c.date5 IS NULL, NULL, date_format(c.date5, '%d.%m.')) as date5,
						if(c.date6 IS NULL, NULL, date_format(c.date6, '%d.%m.')) as date6,
						if(c.date7 IS NULL, NULL, date_format(c.date7, '%d.%m.')) as date7,
						if(c.date8 IS NULL, NULL, date_format(c.date8, '%d.%m.')) as date8,
						if(c.date9 IS NULL, NULL, date_format(c.date9, '%d.%m.')) as date9,
						if(c.date10 IS NULL, NULL, date_format(c.date10, '%d.%m.')) as date10,
						check1,
						check2,
						check3,
						check4,
						check5,
						check6,
						check7,
						check8,
						check9,
						check10
				   from
							  as_courses c
				   inner join as_users u			on c.trainer_id = u.user_id
				   inner join as_locations l		on c.location_id = l.location_id
				   left  join as_categories ca		on c.cat_id = ca.cat_id
				   where c.course_id = " . $p_id . "
					  and     (" . $_SESSION['user_is_organizer'] . " = 1
							or (c.trainer_id = " . $_SESSION['user_id'] . "
								or c.trainer_id2 = " . $_SESSION['user_id'] . ")
				  			or " . $_SESSION['user_is_admin'] . " = 1);");

			if(!$result) echo $db->error;

			$load_date = true;
			$dates = null;
			while($line = $result->fetch_array()) {


				 $dates	= array($line["date1"],
								$line["date2"],
								$line["date3"],
								$line["date4"],
								$line["date5"],
								$line["date6"],
								$line["date7"],
								$line["date8"],
								$line["date9"],
								$line["date10"]);
				$load_date = false;

				$_SESSION["safety_check1"] = $line["check1"];
				$_SESSION["safety_check2"] = $line["check2"];
				$_SESSION["safety_check3"] = $line["check3"];
				$_SESSION["safety_check4"] = $line["check4"];
				$_SESSION["safety_check5"] = $line["check5"];
				$_SESSION["safety_check6"] = $line["check6"];
				$_SESSION["safety_check7"] = $line["check7"];
				$_SESSION["safety_check8"] = $line["check8"];
				$_SESSION["safety_check9"] = $line["check9"];
				$_SESSION["safety_check10"] = $line["check10"];

				$_SESSION["r_registration_code"] = $line["registration_code"];

				?>
				<div id='course-detail-container-in-two-columns'>
					<ul>
						<li><b>Kurs- Nr.: <?=$line['course_id']?></b></li>
						<li>Kursname: <?=$line['kursname']?></li>
						<li>Beginn: <?=$line['begin1']?></li>
						<li>Uhrzeit: <?=$line['time']?></li>
						<li>Trainer: <?=$line['trainer']?></li>
					</ul>
					<ul>
						<li>Termine: <?=$line['termine']?></li>
						<li>Ort: <?=$line['ort']?></li>
						<li>Preis: <?=$line['price']?> €</li>
						<li>Anmeldungen: <?=$line['anmeldungen']?></li>
					</ul>
				</div>

				<?

			}
			if($result->num_rows === 0) {
				if($_SESSION["user_is_organizer"] == "1" ||
		  		   $_SESSION["user_is_admin"] == "1") {
		  		   		echo '<br>Keinen Kurs gefunden.';
		  		   }else{
		  		   		echo "<br>Kein Kurs für " . $_SESSION['user_prename'] . " " .  $_SESSION['user_surname'] . " gefunden.";
		  		   }
				$db->close();
				return array('date1' => NULL);


			}else {
				$db->close();
				return $dates;
			}

			$db->close();
			return array('date1' => NULL);

	} */

    public function db_update_attendance($p_attendance_data)
    {
        if (!isset($p_attendance_data)) return false;

        $db = $this->db_connect();

        foreach ($p_attendance_data as $row) {
            if (!isset($row["registration_id"])) continue;

            if ($pre_stmt = $db->prepare("
			
				UPDATE as_registrations
				   SET present1 = ?,
					   present2 = ?,
					   present3 = ?,
					   present4 = ?,
					   present5 = ?,
					   present6 = ?,
					   present7 = ?,
					   present8 = ?,
					   present9 = ?,
					   present10 = ?,
					   present11 = ?,
					   present12 = ?,
					   mod_dat = now()
				 WHERE registration_id = ?;")
            ) {
                $pre_stmt->bind_param("iiiiiiiiiiiis", $row["a1"],
                    $row["a2"],
                    $row["a3"],
                    $row["a4"],
                    $row["a5"],
                    $row["a6"],
                    $row["a7"],
                    $row["a8"],
                    $row["a9"],
                    $row["a10"],
                    $row["a11"],
                    $row["a12"],
                    $row["registration_id"]);
                $pre_stmt->execute();
                if ($pre_stmt->affected_rows == 0) {
                    // echo "Eintrag nicht gefunden";
                }
            } else {
                echo "E-2033: Fehler: update failed";
                $db->close();
                return false;
            }


        }
        $db->close();
        return true;
    }


    public function db_apply_to_status($p_attendance_data,
                                       $p_course_id)
    {
        if (!isset($p_attendance_data)) return false;

        $db = $this->db_connect();

        foreach ($p_attendance_data as $row) {
            if (!isset($row["registration_id"])) continue;
            if ($pre_stmt = $db->prepare("
			
				UPDATE as_registrations
				   SET present1 = ?,
				   	   status   = IF(? = 1, 3, 21), 
					   mod_dat = now()
				 WHERE registration_id = ?
				   AND status = 2;")
            ) {
                $pre_stmt->bind_param("iis", $row["a1"],
                    $row["a1"],
                    $row["registration_id"]);
                $pre_stmt->execute();
                if ($pre_stmt->affected_rows == 0) {
                    // echo "Eintrag nicht gefunden";
                }


                // speichere alle Anwesenheiten von Termin 1, auch die ungleich status 2
                $pre_stmt2 = $db->prepare("
					UPDATE as_registrations
					   SET present1 = ?, 
						   mod_dat = now()
					 WHERE registration_id = ?");
                $pre_stmt2->bind_param("is", $row["a1"],
                    $row["registration_id"]);
                $pre_stmt2->execute();


            } else {
                echo "E-2033: Fehler: update failed: " . $db->error;
                $db->close();
                return false;
            }


        }


        global $db_functions;
        $db_functions->courses->db_update_actual_count($p_course_id);

        $db->close();
        return true;
    }

    public function db_update_safety_check($p_course_id,
                                           $p_termin_nr,
                                           $p_prename,
                                           $p_surname)
    {

        if (!(isset($p_termin_nr) && is_numeric($p_termin_nr) && $p_termin_nr <= 12 && $p_termin_nr >= 1)) return false;

        $db = $this->db_connect();

        $statement = "
			UPDATE as_courses
			   SET check" . $p_termin_nr . " = 'Bestätigt von $p_prename $p_surname',
			       mod_dat = now()
			 WHERE course_id = $p_course_id;";
        $result = $db->query($statement);
        return $result;
    }

    public function db_reset_safety_check($p_course_id)
    {

        $db = $this->db_connect();

        $statement = "
			UPDATE as_courses
			   SET check1 = NULL,
			   	   check2 = NULL,
			   	   check3 = NULL,
			   	   check4 = NULL,
			   	   check5 = NULL,
			   	   check6 = NULL,
			   	   check7 = NULL,
			   	   check8 = NULL,
			   	   check9 = NULL,
			   	   check10 = NULL,
			   	   check11 = NULL,
			   	   check12 = NULL,
			       mod_dat = now()
			 WHERE course_id = $p_course_id;";
        $result = $db->query($statement);
        return $result;
    }


    public function db_load_table_course_notes($p_course_id, $p_edit = 0)
    {

        global $db_functions;
        $is_kid_course = $db_functions->courses->db_check_if_kid_course($p_course_id);

        $db = $this->db_connect();
        $result = $db->query("SELECT private_remark,
									   public_remark
								  FROM as_courses
								 WHERE course_id = $p_course_id");
        $line = $result->fetch_array();
        $public_remark = $line["public_remark"];
        $private_remark = $line["private_remark"];


        if ($pre_stmt = $db->prepare("
		
			SELECT s.prename,
				   s.surname,
				   s.email,
				   r.rank,
				   r.status,
				   r.public_remark,
				   r.private_remark,
				   r.registration_id,
				   r.kid_name,
				   s.student_id
			  FROM 		as_registrations r
			 INNER JOIN as_students s       ON r.student_id = s.student_id
			 WHERE r.course_id = ?
			   AND r.status IN (2,3,6,7)
			 ORDER BY r.rank")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($prename,
                $surname,
                $email,
                $rank,
                $status,
                $public_remark,
                $private_remark,
                $registration_id,
                $kid_name,
                $student_id);

            echo "<div class='table-notes'><table>\n
		      <tr><th>Name</th>"
                . "<th><div style='padding: 0 10px'>Zahl-<br />status</div></th>"
                . "<th>Notizen</th>";
            if ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1") {
                echo "<th>Notizen</br>(verborgen)</th>";
            }
            echo "</tr>\n";


            echo "<tr edit='-99'>";
            echo "<td>Kurs-<br>Notizen</td>"
                . "<td class='td-center'></div></td>"
                . "<td style='min-width: 370px'><div class='td-overflow'>" .
                (($p_edit == -99) ? "<textarea name='n_public_remark' class='r-input-public-remark' cols='48' rows='7' >" . $public_remark . "</textarea>" : nl2br($public_remark)) . "</div></td>";

            if ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1") {
                echo "<td style='min-width: 370px'><div class='td-overflow'>" .
                    (($p_edit == -99) ? "<textarea name='n_private_remark' class='r-input-private-remark' cols='48' rows='7' >" . $private_remark . "</textarea>" : nl2br($private_remark)) . "</div></td>";
            }

            echo "</tr>\n";

            global $rb_functions;
            while ($pre_stmt->fetch()) {

                switch ($status) {
                    case 3:
                        $status = "<div " . $rb_functions->ignore_android("style='color: green'") . ">✔</div>";
                        break;
                    case 6:
                    case 7:
                        $status = "<div style='font-weight: normal; font-size: 15px; " . $rb_functions->ignore_android("color: green;") . "'>Nach-<br />holer</div>"; // * Nachholer, Sonstiges
                        break;
                    default:
                        $status = "<div " . $rb_functions->ignore_android("style='color: red'") . ">✖</div>";
                        break;
                }


                echo "<tr edit='" . $registration_id . "'>";

                if ($is_kid_course == 1) {
                    echo "<td>" . $kid_name . "<br/>(" . $prename . "<br>" . $surname . ")</td>";

                } else {
                    echo "<td>" . $prename . "<br>" . $surname . "</td>";
                }

                echo "<td class='td-center' style='font-size: 20px; font-weight: bold;'>" . $status . "</div></td>"
                    . "<td style='min-width: 370px'><div class='td-overflow'>" .
                    (($p_edit == $registration_id) ? "<textarea name='n_public_remark' class='r-input-public-remark' cols='48' rows='7' >$public_remark</textarea>" : nl2br($public_remark)) . "</div></td>";

                if ($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1") {
                    echo "<td style='min-width: 370px'><div class='td-overflow'>" .
                        (($p_edit == $registration_id) ? "<textarea name='n_private_remark' class='r-input-private-remark' cols='48' rows='7' >$private_remark</textarea>" : nl2br($private_remark)) . "</div></td>";
                }

                echo "</tr>\n";

            }


            echo "</table></div>";
            if ($pre_stmt->num_rows === 0) echo '</br>Derzeit sind für diesen Kurs noch keine Teilnehmer verbindlich angemeldet.';

            $db->close();

        } else {
            echo "E-2038: Fehler: Laden der Anmeldungen fehlgeschlagen.";
            $db->close();
        }

    }

    public function db_update_notes($p_edit,
                                    $p_course_id,
                                    $p_public_remark,
                                    $p_private_remark)

    {

        // Validation
        $_SESSION["n_error_msg"] = "";
        $p_public_remark = "'" . $p_public_remark . "'";
        $p_private_remark = "'" . $p_private_remark . "'";

        $db = $this->db_connect();

        if ($p_edit == -99) {
            $statement = "
				UPDATE as_courses
				   SET	   public_remark = $p_public_remark,
						   private_remark = $p_private_remark,
						   mod_dat = now()
				WHERE course_id= $p_course_id;";
        } else {

            $statement = "
				UPDATE as_registrations
				   SET	   public_remark = $p_public_remark,
						   private_remark = $p_private_remark,
						   mod_dat = now()
				WHERE registration_id= $p_edit;";
        }

        $result = false;
        $_SESSION["n_success_msg"] = false;
        if (empty($_SESSION["n_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["n_error_msg"] = $db->error . "<br><br>" . $statement;
                $db->close();
                return false;
            } else {

                $_SESSION["n_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_update_course_todo($p_course_id,
                                          $p_todo = true)
    {

        $db = $this->db_connect();
        if (empty($p_todo)) $p_todo = "0";

        $statement = "
				UPDATE as_courses
				   SET	   todo = $p_todo
				WHERE course_id= $p_course_id;";

        $result = $db->query($statement);


        if (!$result) {
            //echo $db->error . "<br>" . $statement . "<br>";
            $db->close();
            return false;
        } else {
            $db->close();
            return true;
        }


    }


}


class DB_Functions_Users extends DB_Connect
{

    public function db_load_table_users($p_prename, $p_surname, $p_login_name, $p_status)
    {

        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2224: No Permission to read Data. Ask your Administrator";
            return false;
        }


        foreach (array("p_prename",
                     "p_surname",
                     "p_login_name") as $var_name) {

            if (empty($$var_name) || $$var_name == "*") $$var_name = "##all";
            else $$var_name = '%' . str_replace('*', '%', $$var_name) . '%';

        }

        if (empty($p_status)) $p_status == -2;


        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		
			SELECT u.user_id,
				   u.login_name,
				   u.prename,
				   u.surname,
				   u.e_mail,
				   u.is_trainer,
				   u.is_organizer,
				   u.is_admin,
				   u.is_enabled,
				   u.sort_no
			  FROM 		as_users u
			 WHERE ('$p_prename' = '##all'
			  			OR u.prename LIKE '$p_prename')
			    AND ('$p_surname' = '##all'
			  			OR u.surname LIKE '$p_surname')
			    AND ('$p_login_name' = '##all'
			  			OR u.login_name LIKE '$p_login_name'
			  			OR u.e_mail LIKE '$p_login_name')
			    AND ($p_status = -2
			  			OR u.is_enabled = $p_status)
			  	AND u.login_name != 'notfall_admin'
			  	AND u.user_id != -1
		      ORDER BY u.sort_no, u.login_name")
        ) {
            $pre_stmt->execute();
            $pre_stmt->bind_result($user_id,
                $login_name,
                $prename,
                $surname,
                $email,
                $is_trainer,
                $is_organizer,
                $is_admin,
                $is_enabled,
                $sort_no);

            echo "<div class='table-users'><table>\n
		      <tr><th>Login-Name</th>"
                . "<th>Vorname</th>"
                . "<th>Nachname</th>"
                . "<th>Email</th>"
                . "<th>Trainer</th>"
                . "<th>Organizer</th>"
                . "<th>Admin</th>"
                . "<th>Status</th>"
                . "</tr>\n";

            while ($pre_stmt->fetch()) {


                foreach (array('is_trainer',
                             'is_organizer',
                             'is_admin',
                             'is_enabled') as $var_name) {

                    switch ($$var_name) {
                        case 1:  // aktiviert
                            $$var_name = "<div style='color: green'>✔</div>";
                            break;
                        case 0:  // deaktiviert
                            if ($var_name == "is_enabled") $$var_name = "<div style='color: red'>✖</div>";
                            else $$var_name = $$var_name = "<div style='color: rgba(255, 0, 0, 0)'>✖</div>"; // transparent 100%
                            break;
                        default:
                            $$var_name = "?";
                            break;
                    }
                }

                echo "<tr user_id='" . $user_id . "'>";
                echo "<td>" . $login_name . "</td>"
                    . "<td>" . $prename . "</td>"
                    . "<td>" . $surname . "</td>"
                    . "<td class='email-text'>" . $email . "</td>"
                    . "<td class='td-center rb-symbols'>" . $is_trainer . "</div></td>"
                    . "<td class='td-center rb-symbols'>" . $is_organizer . "</div></td>"
                    . "<td class='td-center rb-symbols'>" . $is_admin . "</div></td>"
                    . "<td class='td-center rb-symbols'>" . $is_enabled . "</div></td>"
                    . "</tr>\n";
            }
            echo "</table></div>";
            if ($pre_stmt->num_rows === 0) echo '</br>Keine User und Trainer gefunden, bitte andere Suchoptionen wählen.';

            $db->close();
        } else {
            echo "E-2035: Fehler: Laden der User und Trainer fehlgeschlagen.";
            echo "<br>" . $db->error;
            $db->close();
        }
    }


    public function db_load_user_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT user_id,
						login_name,
						prename,
						surname,
						e_mail AS email,
						is_trainer AS trainer,
						is_organizer AS organizer,
						is_admin AS admin,
						is_enabled AS status
				   FROM as_users
			      WHERE user_id = " . $p_id . ";");

        if (!$result) echo $db->error;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            echo '<br>Kein User gefunden.';
            $db->close();
            return;
        } else {

            $_SESSION["u_login_name"] = $line["login_name"];
            $_SESSION["u_prename"] = $line["prename"];
            $_SESSION["u_surname"] = $line["surname"];
            $_SESSION["u_email"] = $line["email"];
            $_SESSION["u_trainer"] = $line["trainer"];
            $_SESSION["u_organizer"] = $line["organizer"];
            $_SESSION["u_admin"] = $line["admin"];
            $_SESSION["u_status"] = $line["status"];
        }
        $db->close();

    }

    public function db_insert_new_user($p_login_name,
                                       $p_prename,
                                       $p_surname,
                                       $p_email,
                                       $p_trainer,
                                       $p_organizer,
                                       $p_admin,
                                       $p_status,
                                       $p_password)
    {

        // Validation
        $_SESSION["u_error_msg"] = "";
        if (empty($p_login_name) || !preg_match('/^[A-Za-z][0-9A-Za-z_]{2,50}$/', $p_login_name)) {
            $_SESSION["u_error_msg"] .= "Bitte einen gültigen Login Namen eingeben.<br>";
        } else {
            $p_login_name = "'" . $p_login_name . "'";
        }
        if (empty($p_prename)) {
            $_SESSION["u_error_msg"] .= "Bitte einen Vornamen eingeben.<br>";
        } else {
            $p_prename = "'" . $p_prename . "'";
        }
        if (empty($p_surname)) {
            $_SESSION["u_error_msg"] .= "Bitte einen Nachnamen eingeben.<br>";
        } else {
            $p_surname = "'" . $p_surname . "'";
        }
        if (empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["u_error_msg"] .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
        } else {
            $p_email = "'" . $p_email . "'";
        }

        if (!(isset($p_trainer) && $p_trainer == 0)) $p_trainer = 1;

        if (!(isset($p_organizer) && $p_organizer == 1)) $p_organizer = 0;

        if (!(isset($p_admin) && $p_admin == 1)) $p_admin = 0;

        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;

        if (empty($p_password) || !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$€%?_*.+-\/])[0-9A-Za-zÄäÜuÖö!@#$€%?*._\/\$+-]{8,50}$/', $p_password)) {
            $_SESSION["u_error_msg"] .= 'Bitte ein gültiges Passwort verwenden.</br>Mindestens ein Großbuchstabe, ein Kleinbuchstabe, eine Zahl,<br/> mindestens 8 Zeichen lang, mindestens ein Sonderzeichen.</br>Erlaubte Sonderzeichen: !@#$€%?_*/$+-.<br/>';
        } else {
            $p_password = "'" . md5($p_password . "b_14") . "'";
        }

        $db = $this->db_connect();

        $s_statement =

            "
			INSERT INTO as_users  (login_name,
								   prename,
								   surname,
								   e_mail,
								   is_trainer,
								   is_organizer,
								   is_admin,
								   is_enabled,
								   password)
								   
			VALUES 				  ($p_login_name,
								   $p_prename,
								   $p_surname,
								   $p_email,
								   $p_trainer,
								   $p_organizer,
								   $p_admin,
								   $p_status,
								   $p_password);";


        $result = false;
        $_SESSION["u_success_msg"] = false;
        if (empty($_SESSION["u_error_msg"])) {
            $result = $db->query($s_statement);
            if (!$result) {
                $_SESSION["u_error_msg"] = $db->error . "<br><br>"; // . $s_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["user_id"] = $db->insert_id;
                $_SESSION["u_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_update_user($p_id,
                                   $p_login_name,
                                   $p_prename,
                                   $p_surname,
                                   $p_email,
                                   $p_trainer,
                                   $p_organizer,
                                   $p_admin,
                                   $p_status)
    {
        // Validation
        $_SESSION["u_error_msg"] = "";
        if (empty($p_login_name) || !preg_match('/^[A-Za-z][0-9A-Za-z_]{2,50}$/', $p_login_name)) {
            $_SESSION["u_error_msg"] .= "Bitte einen gültigen Login Namen eingeben.<br>";
        } else {
            $p_login_name = "'" . $p_login_name . "'";
        }
        if (empty($p_prename)) {
            $_SESSION["u_error_msg"] .= "Bitte einen Vornamen eingeben.<br>";
        } else {
            $p_prename = "'" . $p_prename . "'";
        }
        if (empty($p_surname)) {
            $_SESSION["u_error_msg"] .= "Bitte einen Nachnamen eingeben.<br>";
        } else {
            $p_surname = "'" . $p_surname . "'";
        }
        if (empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["u_error_msg"] .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
        } else {
            $p_email = "'" . $p_email . "'";
        }

        if (!(isset($p_trainer) && $p_trainer == 0)) $p_trainer = 1;

        if (!(isset($p_organizer) && $p_organizer == 1)) $p_organizer = 0;

        if (!(isset($p_admin) && $p_admin == 1)) $p_admin = 0;

        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;


        $db = $this->db_connect();

        $u_statement = "
			UPDATE as_users
			   SET	   login_name = $p_login_name,
					   prename = $p_prename,
					   surname = $p_surname,
					   e_mail = $p_email,
					   is_trainer = $p_trainer,
					   is_organizer = $p_organizer,
					   is_admin = $p_admin,
					   is_enabled = $p_status,
					   mod_dat = now()
			WHERE user_id= $p_id;";

        $result = false;
        $_SESSION["u_success_msg"] = false;
        if (empty($_SESSION["u_error_msg"])) {
            $result = $db->query($u_statement);
            if (!$result) {
                $_SESSION["u_error_msg"] = $db->error . "<br><br>" . $u_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["u_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔ </p>";

                $db->close();
                return true;
            }
        }

    }


    public function change_user_password($p_id,
                                         $p_password,
                                         $p_password_from_admin)
    {


        $db = $this->db_connect();

        // Validation
        $_SESSION["u_error_msg"] = "";
        $result = $db->query("SELECT login_name
									FROM as_users
									WHERE login_name = '" . $_SESSION["user_name"] . "'
									  AND is_enabled = TRUE
									  AND password = '" . md5($p_password_from_admin . "b_14") . "';");

        if ($result->num_rows === 0) $_SESSION["u_error_msg"] .= "Administrator- Passwort nicht korrekt.<br/>";


        if (empty($p_password) || !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$€%?_*.+-\/])[0-9A-Za-zÄäÜuÖö!@#$€%?*._\/\$+-]{8,50}$/', $p_password)) {
            $_SESSION["u_error_msg"] .= 'Bitte ein gültiges Passwort verwenden.</br>Mindestens ein Großbuchstabe, ein Kleinbuchstabe, eine Zahl,<br/> mindestens 8 Zeichen lang, mindestens ein Sonderzeichen.</br>Erlaubte Sonderzeichen: !@#$€%?_*/$+-.<br/>';
        } else {
            $p_password = "'" . md5($p_password . "b_14") . "'";
        }


        $u_statement = "
			UPDATE as_users
			   SET	   password = $p_password,
					   mod_dat = now()
			WHERE user_id= $p_id;";

        $result = false;
        $_SESSION["u_success_msg"] = false;
        if (empty($_SESSION["u_error_msg"])) {
            $result = $db->query($u_statement);
            if (!$result) {
                $_SESSION["u_error_msg"] = $db->error . "<br><br>" . $u_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["u_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Passwort gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }


}

class DB_Functions_Categories extends DB_Connect
{

    public function db_load_table_categories($p_name, $p_status)
    {
        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2224: No Permission to read Data. Ask your Administrator";
            return false;
        }
        if (empty($p_name) || $p_name == "*") {
            $p_name = "##all";
        } else {
            $p_name = '%' . str_replace('*', '%', $p_name) . '%';
        }
        if (!isset($p_status)) $p_status = 1;
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT cat_id,
					title,
					sort_no,
					is_enabled as status
			   from as_categories
			  WHERE ($p_status = -2
			  			OR is_enabled = $p_status)
			  	AND ('$p_name' = '##all' OR
			  		title LIKE '$p_name')
		      ORDER BY sort_no");
        if (!$result) echo $db->error;
        echo "<div class='table-categories'><table>\n
		      <tr><th>Sortierung</th>"
            . "<th>Kategoriename</th>"
            . "<th>Status</th></tr>";
        while ($line = $result->fetch_array()) {
            switch ($line["status"]) {
                case 1:  // aktiviert
                    $line["status"] = "<div style='color: green'>✔</div>";
                    break;
                case 0:  // deaktiviert
                    $line["status"] = "<div style='color: red'>✖</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }
            echo "<tr cat_id='" . $line["cat_id"] . "'>"
                . "<td><div class='td-center'>" . htmlspecialchars($line["sort_no"]) . "</div></td>"
                . "<td><div class='td-overflow' style='min-width: 200px'>" . $line["title"] . "</div>"
                . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                . "</td></tr>\n";
        }
        echo "</table></div>";
        if ($result->num_rows === 0) echo '<br>Keine Kategorie vorhanden.';
        $db->close();
    }

    public function db_load_category_values_from_id($p_id)
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT title,
						sort_no,
						has_sub_cat,
						is_enabled AS status
				   FROM as_categories
			      WHERE cat_id = " . $p_id . ";");
        if (!$result) echo $db->error;
        $line = $result->fetch_array();
        if ($result->num_rows === 0) {
            echo '<br>Keine Kategorie gefunden.';
            $db->close();
            return;
        } else {
            $_SESSION["ca_name"] = $line["title"];
            $_SESSION["ca_sort_no"] = $line["sort_no"];
            $_SESSION["ca_has_sub_cat"] = $line["has_sub_cat"];
            $_SESSION["ca_status"] = $line["status"];
        }
        $db->close();
    }

    public function db_insert_new_category($p_name,
                                           $p_sort_no,
                                           $p_has_sub_cat,
                                           $p_status)
    {
        // Validation
        $_SESSION["ca_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["ca_error_msg"] .= "Bitte einen Kategorienamen eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (!(isset($p_sort_no) && is_numeric($p_sort_no))) $p_sort_no = 50;
        if (!(isset($p_has_sub_cat) && $p_has_sub_cat == 0)) $p_has_sub_cat = 1;
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;
        $db = $this->db_connect();
        $ca_statement =
            "
			INSERT INTO as_categories (title,
									   sort_no,
									   has_sub_cat,
									   is_enabled)
								   
			VALUES 				  	  ($p_name,
									   $p_sort_no,
									   $p_has_sub_cat,
									   $p_status);";
        $result = false;
        $_SESSION["ca_success_msg"] = false;
        if (empty($_SESSION["ca_error_msg"])) {
            $result = $db->query($ca_statement);
            if (!$result) {
                $_SESSION["ca_error_msg"] = $db->error . "<br><br>"; // . $ca_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["cat_id"] = $db->insert_id;
                $_SESSION["ca_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }
    }


    public function db_update_category($p_id,
                                       $p_name,
                                       $p_sort_no,
                                       $p_has_sub_cat,
                                       $p_status)
    {
        // Validation
        $_SESSION["ca_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["ca_error_msg"] .= "Bitte einen Kategorienamen eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (!(isset($p_sort_no) && is_numeric($p_sort_no))) $_SESSION["ca_error_msg"] .= "Sortierung muss eine Zahl sein.<br>";
        if (!(isset($p_has_sub_cat) && $p_has_sub_cat == 0)) $p_has_sub_cat = 1;
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;
        $db = $this->db_connect();
        $statement = "
			UPDATE as_categories
			   SET	   title = $p_name,
					   sort_no = $p_sort_no,
					   has_sub_cat = $p_has_sub_cat,
					   is_enabled = $p_status,
					   mod_dat = now()
			WHERE cat_id = $p_id;";
        $result = false;
        $_SESSION["ca_success_msg"] = false;
        if (empty($_SESSION["ca_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["ca_error_msg"] = $db->error . "<br><br>"; // . $ca_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["ca_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
                $db->close();
                return true;
            }
        }

    }
}


class DB_Functions_Subategories extends DB_Connect
{


    public function db_load_table_subcategories($p_category, $p_status)
    {

        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2225: No Permission to read Data. Ask your Administrator";
            return false;
        }

        if (empty($p_category)) $p_category = "1";

        if (!isset($p_status)) $p_status = 1;


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT sc.cat_id,
					sc.subcat_id,
					c.title,
					sc.subtitle,
					sc.sort_no,
					sc.is_enabled as status
			   from as_subcategories sc
			  inner join as_categories c on c.cat_id = sc.cat_id
			  WHERE ($p_status = -2
			  			OR sc.is_enabled = $p_status)
			  	AND sc.cat_id = $p_category
		      ORDER BY sc.sort_no");


        if (!$result) echo $db->error;

        echo "<div class='table-subcategories'><table>\n
		      <tr><th>Sortierung</th>"
            . "<th>Kategorie</th>"
            . "<th>Subkategoriename</th>"
            . "<th>Status</th></tr>";

        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 1:  // aktiviert
                    $line["status"] = "<div style='color: green'>✔</div>";
                    break;
                case 0:  // deaktiviert
                    $line["status"] = "<div style='color: red'>✖</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }

            echo "<tr subcat_id='" . $line["subcat_id"] . "'>"
                . "<td><div class='td-center'>" . htmlspecialchars($line["sort_no"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . $line["title"] . "</div>"
                . "<td><div class='td-overflow' style='min-width: 200px'>" . $line["subtitle"] . "</div>"
                . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                . "</td></tr>\n";
        }
        echo "</table></div>";
        if ($result->num_rows === 0) echo '<br>Keine Subkategorie vorhanden.';
        $db->close();
    }

    public function db_load_subcategory_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT subtitle,
						sort_no,
						cat_id,
						is_enabled AS status,
						cat_id,
						is_kid_course,
						auto_unsubscribe,
						confirmation_text,
						picture_count,
						picture_filename1,
						picture_filename2,
						picture_filename3,
						description_long
				   FROM as_subcategories
			      WHERE subcat_id = " . $p_id . ";");

        if (!$result) echo $db->error;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            echo '<br>Keine Subkategorie gefunden.';
            $db->close();
            return;
        } else {

            $_SESSION["sca_name"] = $line["subtitle"];
            $_SESSION["sca_sort_no"] = $line["sort_no"];
            $_SESSION["sca_cat_id"] = $line["cat_id"];
            $_SESSION["sca_status"] = $line["status"];
            $_SESSION["sca_category"] = $line["cat_id"];
            $_SESSION["sca_is_kid_course"] = $line["is_kid_course"];
            $_SESSION["sca_auto_unsubscribe"] = $line["auto_unsubscribe"];
            $_SESSION["sca_conf_text"] = $line["confirmation_text"];
            $_SESSION["sca_pictures"] = $line["picture_count"];
            $_SESSION["sca_filename_picture_1"] = $line["picture_filename1"];
            $_SESSION["sca_filename_picture_2"] = $line["picture_filename2"];
            $_SESSION["sca_filename_picture_3"] = $line["picture_filename3"];
            $_SESSION["sca_description"] = $line["description_long"];
        }
        $db->close();

    }

    public function db_insert_new_subcategory($p_name,
                                              $p_category,
                                              $p_sort_no,
                                              $p_status,
                                              $p_picture_count,
                                              $p_picture_filename1,
                                              $p_picture_filename2,
                                              $p_picture_filename3,
                                              $p_description,
                                              $p_is_kid_course,
                                              $p_auto_unsubscribe,
                                              $p_conf_text)
    {

        // Validation
        $_SESSION["sca_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["sca_error_msg"] .= "Bitte einen Kategorienamen eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (!(isset($p_category) && is_numeric($p_sort_no))) {
            $_SESSION["sca_error_msg"] .= "Bitte Kategorie wählen.<br>";
        }
        if (!(isset($p_sort_no) && is_numeric($p_sort_no))) $p_sort_no = 50;
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;
        if (!(isset($p_is_kid_course) && $p_is_kid_course == 1)) $p_is_kid_course = 0;
        if (!(isset($p_auto_unsubscribe) && $p_auto_unsubscribe == 1)) $p_auto_unsubscribe = 0;
        if (empty($p_conf_text)) {
            $p_conf_text = "NULL";
        } else {
            $p_conf_text = "'" . $p_conf_text . "'";
        }
        if (empty($p_picture_filename1)) {
            $p_picture_filename1 = "NULL";
            if ($p_picture_count > 0) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 1 angeben.<br>";
        } else {
            $p_picture_filename1 = "'" . $p_picture_filename1 . "'";
        }
        if (empty($p_picture_filename2)) {
            $p_picture_filename2 = "NULL";
            if ($p_picture_count > 1) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 2 angeben.<br>";
        } else {
            $p_picture_filename2 = "'" . $p_picture_filename2 . "'";
        }
        if (empty($p_picture_filename3)) {
            $p_picture_filename3 = "NULL";
            if ($p_picture_count > 2) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 3 angeben.<br>";
        } else {
            $p_picture_filename3 = "'" . $p_picture_filename3 . "'";
        }
        if (!(isset($p_picture_count) && is_numeric($p_picture_count))) $p_picture_count = 0;

        if (empty($p_description)) {
            $p_description = "NULL";
        } else {
            $p_description = "'" . $p_description . "'";
        }

        $db = $this->db_connect();
        $ca_statement =

            "
			INSERT INTO as_subcategories ( subtitle,
										   cat_id,
										   sort_no,
										   is_enabled,
										   picture_count,
										   picture_filename1,
										   picture_filename2,
										   picture_filename3,
										   description_long,
										   is_kid_course,
										   auto_unsubscribe,
										   confirmation_text)
								   
			VALUES 				  	  ($p_name,
									   $p_category,
									   $p_sort_no,
									   $p_status,
									   $p_picture_count,
									   $p_picture_filename1,
									   $p_picture_filename2,
									   $p_picture_filename3,
									   $p_description,
									   $p_is_kid_course,
									   $p_auto_unsubscribe,
									   $p_conf_text);";
        $result = false;
        $_SESSION["sca_success_msg"] = false;
        if (empty($_SESSION["sca_error_msg"])) {
            $result = $db->query($ca_statement);
            if (!$result) {
                $_SESSION["sca_error_msg"] = $db->error . "<br><br>"; // . $sca_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["subcat_id"] = $db->insert_id;
                $_SESSION["sca_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_update_subcategory($p_id,
                                          $p_name,
                                          $p_category,
                                          $p_sort_no,
                                          $p_status,
                                          $p_picture_count,
                                          $p_picture_filename1,
                                          $p_picture_filename2,
                                          $p_picture_filename3,
                                          $p_description,
                                          $p_is_kid_course,
                                          $p_auto_unsubscribe,
                                          $p_conf_text)
    {


        // Validation
        $_SESSION["sca_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["sca_error_msg"] .= "Bitte den Namen der Unterkategorien eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (!(isset($p_sort_no) && is_numeric($p_sort_no))) $_SESSION["sca_error_msg"] .= "Sortierung muss eine Zahl sein.<br>";
        if (!(isset($p_category) && is_numeric($p_category))) $_SESSION["sca_error_msg"] .= "Bitte gültige Kategorie auswählen.<br>";
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;
        if (!(isset($p_is_kid_course) && $p_is_kid_course == 1)) $p_is_kid_course = 0;
        if (!(isset($p_auto_unsubscribe) && $p_auto_unsubscribe == 1)) $p_auto_unsubscribe = 0;
        if (empty($p_conf_text)) {
            $p_conf_text = "NULL";
        } else {
            $p_conf_text = "'" . $p_conf_text . "'";
        }

        if (!(isset($p_picture_count) && is_numeric($p_picture_count))) $p_picture_count = 0;

        if (empty($p_picture_filename1)) {
            $p_picture_filename1 = "NULL";
            if ($p_picture_count > 0) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 1 angeben.<br>";
        } else {
            $p_picture_filename1 = "'" . $p_picture_filename1 . "'";
        }
        if (empty($p_picture_filename2)) {
            $p_picture_filename2 = "NULL";
            if ($p_picture_count > 1) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 2 angeben.<br>";
        } else {
            $p_picture_filename2 = "'" . $p_picture_filename2 . "'";
        }
        if (empty($p_picture_filename3)) {
            $p_picture_filename3 = "NULL";
            if ($p_picture_count > 2) $_SESSION["sca_error_msg"] .= "Bitte Dateiname von Bild 3 angeben.<br>";
        } else {
            $p_picture_filename3 = "'" . $p_picture_filename3 . "'";
        }

        if (empty($p_description)) {
            $p_description = "NULL";
        } else {
            $p_description = "'" . $p_description . "'";
        }


        $db = $this->db_connect();

        $statement = "
			UPDATE as_subcategories
			   SET	   subtitle = $p_name,
					   sort_no = $p_sort_no,
					   cat_id = $p_category,
					   is_enabled = $p_status,
					   picture_count = $p_picture_count,
					   picture_filename1 = $p_picture_filename1,
					   picture_filename2 = $p_picture_filename2,
					   picture_filename3 = $p_picture_filename3,
					   description_long = $p_description,
					   is_kid_course = $p_is_kid_course,
					   auto_unsubscribe = $p_auto_unsubscribe,
					   confirmation_text = $p_conf_text,
					   mod_dat = now()
			WHERE subcat_id = $p_id;";

        $result = false;
        $_SESSION["sca_success_msg"] = false;
        if (empty($_SESSION["sca_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["sca_error_msg"] = $db->error . "<br><br>"; // . $sca_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["sca_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }
    }
}


class DB_Functions_Locations extends DB_Connect
{


    public function db_load_table_locations($p_name, $p_status)
    {

        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2228: No Permission to read Data. Ask your Administrator";
            return false;
        }


        foreach (array("p_name") as $var_name) {   // bleibt mal so falls noch Parameter hinzukommen

            if (empty($$var_name) || $$var_name == "*") $$var_name = "##all";
            else $$var_name = '%' . str_replace('*', '%', $$var_name) . '%';

        }

        if (empty($p_status)) $p_status == -2;


        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		
			SELECT location_id,
				   short_name,
				   location_name,
				   sort_no,
				   is_enabled
			  FROM 		as_locations l
			 WHERE ('$p_name' = '##all'
			  			OR l.short_name LIKE '$p_name'
			  			OR l.location_name LIKE '$p_name')
			    AND ($p_status = -2
			  			OR l.is_enabled = $p_status)
		      ORDER BY l.sort_no, l.short_name, l.location_name")
        ) {
            $pre_stmt->execute();
            $pre_stmt->bind_result($location_id,
                $short_name,
                $location_name,
                $sort_no,
                $is_enabled);

            echo "<div class='table-locations'><table>\n
		      <tr><th>Sortierung</th>"
                . "<th>Kurzname</th>"
                . "<th>Langer Name</th>"
                . "<th>Status</th>"
                . "</tr>\n";

            while ($pre_stmt->fetch()) {


                foreach (array('is_enabled') as $var_name) {   // bleibt mal so falls noch Parameter hinzukommen

                    switch ($$var_name) {
                        case 1:  // aktiviert
                            $$var_name = "<div style='color: green'>✔</div>";
                            break;
                        case 0:  // deaktiviert
                            $$var_name = "<div style='color: red'>✖</div>";
                            break;
                        default:
                            $$var_name = "?";
                            break;
                    }
                }

                echo "<tr location_id='" . $location_id . "'>";
                echo "<td class='td-center'>" . $sort_no . "</td>"
                    . "<td>" . $short_name . "</td>"
                    . "<td>" . $location_name . "</td>"
                    . "<td class='td-center rb-symbols'>" . $is_enabled . "</div></td>"
                    . "</tr>\n";
            }
            echo "</table></div>";
            if ($pre_stmt->num_rows === 0) echo '</br>Keine Standorte gefunden, bitte andere Suchoptionen wählen.';

            $db->close();
        } else {
            echo "E-2035: Fehler: Laden der Standorte fehlgeschlagen.";
            echo "<br>" . $db->error;
            $db->close();
        }
    }


    public function db_load_location_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $query = "SELECT location_id,
						location_name,
						short_name,
						sort_no,
						street,
						street_number,
						plz,
						ort,
						is_enabled AS status
				   FROM as_locations
			      WHERE location_id = " . $p_id . ";";

        $result = $db->query($query);

        if (!$result) echo $db->error . "<br/>" . $query;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            echo '<br>Kein Standort gefunden.';
            $db->close();
            return;
        } else {

            $_SESSION["l_short_name"] = $line["short_name"];
            $_SESSION["l_location_name"] = $line["location_name"];
            $_SESSION["l_sort_no"] = $line["sort_no"];
            $_SESSION["l_street"] = $line["street"];
            $_SESSION["l_street_number"] = $line["street_number"];
            $_SESSION["l_plz"] = $line["plz"];
            $_SESSION["l_ort"] = $line["ort"];
            $_SESSION["l_status"] = $line["status"];
        }
        $db->close();

    }

    public function db_insert_new_location($p_location_name,
                                           $p_short_name,
                                           $p_sort_no,
                                           $p_street,
                                           $p_street_number,
                                           $p_plz,
                                           $p_ort,
                                           $p_status)
    {

        // Validation
        $_SESSION["l_error_msg"] = "";
        if (empty($p_short_name)) {
            $_SESSION["l_error_msg"] .= "Bitte einen Kurznamen für den Standort eingeben.<br>";
        } else {
            $p_short_name = "'" . $p_short_name . "'";
        }
        if (empty($p_location_name)) {
            $_SESSION["l_error_msg"] .= "Bitte einen Namen(lang) für den Standort angeben.<br>";
        } else {
            $p_location_name = "'" . $p_location_name . "'";
        }
        if (!(!empty($p_sort_no) && is_numeric($p_sort_no))) {
            $_SESSION["l_error_msg"] .= "Bitte eine gültige Sortierungsnummer eingeben.<br>";
        } else {
            $p_sort_no = "'" . $p_sort_no . "'";
        }
        if (empty($p_street)) {
            $_SESSION["l_error_msg"] .= "Bitte die Straße angeben.<br>";
        } else {
            $p_street = "'" . $p_street . "'";
        }
        if (empty($p_street_number)) {
            $_SESSION["l_error_msg"] .= "Bitte die Straßenummer angeben.<br>";
        } else {
            $p_street_number = "'" . $p_street_number . "'";
        }
        if (empty($p_ort)) {
            $_SESSION["l_error_msg"] .= "Bitte den Ort angeben.<br>";
        } else {
            $p_ort = "'" . $p_ort . "'";
        }
        $p_plz = "'" . $p_plz . "'";
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;


        $db = $this->db_connect();

        $l_statement =

            "
			INSERT INTO as_locations  (location_name,
									   short_name,
									   sort_no,
									   street,
									   street_number,
									   plz,
									   ort,
									   is_enabled)
								   
			VALUES 				 ($p_location_name,
								  $p_short_name,
								  $p_sort_no,
								  $p_street,
								  $p_street_number,
								  $p_plz,
								  $p_ort,
								  $p_status);";


        $result = false;
        $_SESSION["l_success_msg"] = false;
        if (empty($_SESSION["l_error_msg"])) {
            $result = $db->query($l_statement);
            if (!$result) {
                $_SESSION["l_error_msg"] = $db->error . "<br><br>" . $l_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["user_id"] = $db->insert_id;
                $_SESSION["l_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }


    public function db_update_location($p_id,
                                       $p_location_name,
                                       $p_short_name,
                                       $p_sort_no,
                                       $p_street,
                                       $p_street_number,
                                       $p_plz,
                                       $p_ort,
                                       $p_status)
    {

        // Validation
        $_SESSION["l_error_msg"] = "";
        if (empty($p_short_name)) {
            $_SESSION["l_error_msg"] .= "Bitte einen Kurznamen für den Standort eingeben.<br>";
        } else {
            $p_short_name = "'" . $p_short_name . "'";
        }
        if (empty($p_location_name)) {
            $_SESSION["l_error_msg"] .= "Bitte einen Namen(lang) für den Standort angeben.<br>";
        } else {
            $p_location_name = "'" . $p_location_name . "'";
        }
        if (!(!empty($p_sort_no) && is_numeric($p_sort_no))) {
            $_SESSION["l_error_msg"] .= "Bitte eine gültige Sortierungsnummer eingeben.<br>";
        } else {
            $p_sort_no = "'" . $p_sort_no . "'";
        }
        if (empty($p_street)) {
            $_SESSION["l_error_msg"] .= "Bitte die Straße angeben.<br>";
        } else {
            $p_street = "'" . $p_street . "'";
        }
        if (empty($p_street_number)) {
            $_SESSION["l_error_msg"] .= "Bitte die Straßenummer angeben.<br>";
        } else {
            $p_street_number = "'" . $p_street_number . "'";
        }
        if (empty($p_ort)) {
            $_SESSION["l_error_msg"] .= "Bitte den Ort angeben.<br>";
        } else {
            $p_ort = "'" . $p_ort . "'";
        }
        $p_plz = "'" . $p_plz . "'";
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;

        $db = $this->db_connect();

        $l_statement = "
			UPDATE as_locations
			   SET	   location_name = $p_location_name,
					   short_name = $p_short_name,
					   sort_no = $p_sort_no,
					   street = $p_street,
					   street_number = $p_street_number,
					   plz = $p_plz,
					   ort = $p_ort,
					   is_enabled = $p_status,
					   mod_dat = now()
			WHERE location_id= $p_id;";


        $result = false;
        $_SESSION["l_success_msg"] = false;
        if (empty($_SESSION["l_error_msg"])) {
            $result = $db->query($l_statement);
            if (!$result) {
                $_SESSION["l_error_msg"] = $db->error . "<br><br>" . $l_statement;
                $db->close();
                return false;
            } else {
                $_SESSION["l_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

}

class DB_Functions_Extras extends DB_Connect
{

    public function db_load_welcome_msg()
    {
        $db = $this->db_connect();

        $statement = "
			SELECT value_long_char AS welcome_msg
			  FROM as_parameter
			 WHERE parameter_name = 'welcome-msg'";
        $result = $db->query($statement);
        $line = $result->fetch_array();
        $_SESSION["m_welcome_msg"] = $line["welcome_msg"];
    }

    public function db_update_welcome_msg($p_welcome_msg)
    {
        $db = $this->db_connect();

        $statement = "
			UPDATE as_parameter
			   SET value_long_char = '$p_welcome_msg',
			   	   mod_dat = now()
			 WHERE parameter_name = 'welcome-msg';";
        $db->query($statement);
    }

}

class DB_Functions_Notes extends DB_Connect
{


    public function db_insert_new_note($p_note_name,
                                       $p_note_text,
                                       $p_status)
    {

        // Validation
        $_SESSION["nt_error_msg"] = "";
        if (empty($p_note_name)) {
            $_SESSION["nt_error_msg"] .= "Bitte einen Namen für die Notiz eingeben.<br>";
        }
        if (empty($p_note_text)) $p_note_text = "";

        $db = $this->db_connect();

        $p_note_text = $db->real_escape_string($p_note_text);
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;

        $statement =

            "
			INSERT INTO as_notes 	  (note_name,
									   note_text,
									   status)
								   
			VALUES 				  	  ('$p_note_name',
									   '$p_note_text',
									   $p_status);";
        $result = false;
        $_SESSION["nt_success_msg"] = false;
        if (empty($_SESSION["nt_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["nt_error_msg"] = $db->error . "<br><br>";
                $db->close();
                return false;
            } else {
                $_SESSION["note_id"] = $db->insert_id;
                $_SESSION["nt_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_update_note($p_id,
                                   $p_name,
                                   $p_text,
                                   $p_status)
    {


        // Validation
        $_SESSION["nt_error_msg"] = "";
        if (empty($p_name)) {
            $_SESSION["nt_error_msg"] .= "Bitte einen Namen der Notiz eingeben.<br>";
        } else {
            $p_name = "'" . $p_name . "'";
        }
        if (!(isset($p_status) && $p_status == 0)) $p_status = 1;
        if (empty($p_text)) {
            $p_text = "''";
        } else {
            $p_text = "'" . $p_text . "'";
        }
        $db = $this->db_connect();

        $statement = "
			UPDATE as_notes
			   SET	   note_name = $p_name,
					   note_text = $p_text,
					   status = $p_status,
					   mod_dat = now()
			WHERE note_id = $p_id;";

        $result = false;
        $_SESSION["nt_success_msg"] = false;
        if (empty($_SESSION["nt_error_msg"])) {
            $result = $db->query($statement);
            if (!$result) {
                $_SESSION["nt_error_msg"] = $db->error . "<br><br>"; // . $statement;
                $db->close();
                return false;
            } else {
                $_SESSION["nt_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";

                $db->close();
                return true;
            }
        }

    }

    public function db_load_note_values_from_id($p_id)
    {


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT note_name,
						status,
						note_text
				   FROM as_notes
			      WHERE note_id = " . $p_id . ";");

        if (!$result) echo $db->error;


        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            echo '<br>Keine Notizen gefunden.';
            $db->close();
            return;
        } else {

            $_SESSION["nt_name"] = $line["note_name"];
            $_SESSION["nt_status"] = $line["status"];
            $_SESSION["nt_note_text"] = $line["note_text"];
        }
        $db->close();

    }

    public function db_load_table_notes($p_name, $p_status)
    {

        if ($_SESSION["user_is_organizer"] != "1" &&
            $_SESSION["user_is_admin"] != "1"
        ) {
            echo "E-2224: No Permission to read Data. Ask your Administrator";
            return false;
        }

        if (empty($p_name) || $p_name == "*") {
            $p_name = "##all";
        } else {
            $p_name = '%' . str_replace('*', '%', $p_name) . '%';
        }

        if (!isset($p_status)) $p_status = 1;


        $db = $this->db_connect();
        $result = $db->query(

            "SELECT note_id,
					note_name,
					Concat(trim(substr(note_text, 1, 60)), '...') as note_text,
					status
			   from as_notes
			  WHERE ($p_status = -2
			  			OR status = $p_status)
			  	AND ('$p_name' = '##all' OR
			  		note_name LIKE '$p_name')
		      ORDER BY sort_no, note_name");


        if (!$result) echo $db->error;

        echo "<div class='table-notes'><table>\n
		      <tr><th style='min-width: 150px'>Notizname</th>"
            . "<th style='min-width: 200px'>Text</th>"
            . "<th style='min-width: 50px'>Status</th></tr>";

        while ($line = $result->fetch_array()) {

            switch ($line["status"]) {
                case 1:  // aktiviert
                    $line["status"] = "<div style='color: green'>✔</div>";
                    break;
                case 0:  // deaktiviert
                    $line["status"] = "<div style='color: red'>✖</div>";
                    break;
                default:
                    $line["status"] = "?";
                    break;
            }


            echo "<tr note_id='" . $line["note_id"] . "'>"
                . "<td><div>" . htmlspecialchars($line["note_name"]) . "</div></td>"
                . "<td><div class='td-overflow'>" . $line["note_text"] . "</div>"
                . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
                . "</td></tr>\n";
        }
        echo "</table></div>";
        if ($result->num_rows === 0) echo '<br>Keine Verwaltungsnotizen vorhanden.';
        $db->close();
    }

    public function db_lock_note($p_note_id,
                                 $p_user_id)
    {


        $db = $this->db_connect();

        $statement = "
			SELECT 1
			  FROM as_notes
			 WHERE note_id = $p_note_id
			   AND lock_user NOT IN ($p_user_id, 0)
			   AND now() < lock_dat + INTERVAL 30 MINUTE;
			";

        $result = $db->query($statement);

        if (!$result) echo $db->error;

        $line = $result->fetch_array();

        if ($result->num_rows === 0) {
            $statement = "
				UPDATE as_notes
				   SET lock_user = $p_user_id,
				   	   lock_dat = now()
				 WHERE note_id = $p_note_id;";

            $result = $db->query($statement);

            if (!$result) echo $db->error;

            return false;
        } else {

            return true; // do lock input fields
        }
    }

    public function db_unlock_note_manually($p_note_id)
    {
        $db = $this->db_connect();

        $statement = "
				UPDATE as_notes
				   SET lock_user = 0
				 WHERE note_id = $p_note_id;";

        $result = $db->query($statement);
    }

    public function db_unlock_note($p_note_id, $p_user_id)
    {
        $db = $this->db_connect();

        $statement = "
				UPDATE as_notes
				   SET lock_user = 0
				 WHERE note_id = $p_note_id
				   AND lock_user = $p_user_id;";

        $result = $db->query($statement);
    }

}

$db_functions = new DB_Functions();

?>