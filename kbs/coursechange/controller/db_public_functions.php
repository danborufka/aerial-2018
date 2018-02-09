<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();

class DB_Public_Connect
{

    public function db_connect()
    {
        global $rb_configuration;
        $mysqli = new mysqli($rb_configuration->db_url, $rb_configuration->db_user, $rb_configuration->db_pwd, $rb_configuration->db_name);
        if ($mysqli->connect_error) {
            echo "Fehler bei der Verbindung zur Datenbank.";
            exit();
        }
        if (!$mysqli->set_charset("utf8")) {
            echo "Datenbankfehler - Fehler beim Laden von UTF8.";
            exit();
        }
        return $mysqli;
    }

    public function db_close()
    {
        if (isset($mysqli)) $mysqli->close();
    }
}

class DB_Public_Functions extends DB_Public_Connect
{


    public $public_coursechange = null;

    public function __construct()
    {
        $this->public_coursechange = new DB_Functions_Public_CourseChange();
    }


}


class DB_Functions_Public_CourseChange extends DB_Public_Connect
{

    public function public_db_getdetails($p_code)
    {
        $db = $this->db_connect();
        $statement = "select concat(s.prename, ' ', s.surname) as course_member , c.name as course_title,
                              date_format(c.date1, '%d.%m.%Y %H:%i') AS course_date, l.location_name as course_location 
                        from as_registrations r
                        inner join as_students s on r.student_id = s.student_id
                        inner join as_courses c on r.course_id = c.course_id
                        inner join as_locations l on l.location_id = c.location_id
                      where r.registration_code = '$p_code' and c.begin > (now() + INTERVAL 30 MINUTE) and r.status = 2";

        $result = $db->query($statement);

        if (!$result) return array('error' => $db->error);
        $line = $result->fetch_array();
        if ($result->num_rows === 0) {
            $db->close();
            return array('error' => "Ungültiger Link oder Kursbegin in den nächsten 30 Minuten.");
        }
        $db->close();
        return $line;


//        $statement = "UPDATE as_students SET newsletter = 0 WHERE email = '$p_course'";
//        $result = $db->query($statement);

    }

    public function public_db_validatemember($p_newemail, $p_code)
    {
        $db = $this->db_connect();
        $statement = "SELECT s.student_id as student_id, concat(s.prename, ' ', s.surname) as name, s.email as email, s.security_training as security_training
                        FROM as_students s 
                        WHERE s.email = '$p_newemail'
                        AND s.status = 1";
        $result = $db->query($statement);
        if (!$result) return array('error' => $db->error);
        $line = $result->fetch_array();
        if ($result->num_rows === 0) {
            $db->close();
            return array('error' => "Ungültige Email Adresse");
        }
        $db->close();
        if ($line["security_training"] == 0) {
            return array('security' => "Mitglied hat noch kein Sicherheitstraining absolviert", 'success' => false);
        } else {
            return array('error' => "Email an " . $line["name"] . " wurde versandt (" . $line["email"] . ").", 'success' => true, 'email' => $line["email"], 'student_id' => $line["student_id"]);
        }
    }
}

$db_public_functions = new DB_Public_Functions();

?>