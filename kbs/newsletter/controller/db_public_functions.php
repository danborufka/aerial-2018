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


    public $public_unsubsribe = null;

    public function __construct()
    {
        $this->public_unsubsribe = new DB_Functions_Public_Unsubscribe();
    }


}


class DB_Functions_Public_Unsubscribe extends DB_Public_Connect
{

    public function public_db_unsubscribe($p_email)
    {
        $db = $this->db_connect();
        $statement = "SELECT newsletter FROM as_students WHERE email = '$p_email'";

        $result = $db->query($statement);

        if (!$result) return array('error' => $db->error);
        $line = $result->fetch_array();
        if ($result->num_rows === 0) {
            $db->close();
            return array('error' => "Diese Email Adresse existiert nicht");
        }
        if($line["newsletter"] == 0){
            $db->close();
            return array('alreadyunsubscribed' => "Bereits von Newsletter abgemeldet");
        }


        $statement = "UPDATE as_students SET newsletter = 0 WHERE email = '$p_email'";
        $result = $db->query($statement);
        $db->close();
    }
}

$db_public_functions = new DB_Public_Functions();

?>