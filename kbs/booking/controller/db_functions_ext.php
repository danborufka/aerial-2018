<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 							 */
/* office@breitschopf.wien			www.breitschopf.wien 					 */
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();


class DB_Functions_Ext extends DB_Connect{

	public $blocks = null;

	public function __construct() {
		$this->blocks = new DB_Functions_Blocks();
	}
}

class DB_Functions_Blocks extends DB_Connect {


	public function db_load_table_blocks($p_email, $p_prename, $p_surname, $p_pay_status, $p_consumption_status, $p_status){ // todo1

		if($_SESSION["user_is_organizer"] != "1" &&
		   $_SESSION["user_is_admin"] != "1")
		{
			echo "E-225: No Permission to read Data. Ask your Administrator";
			return false;
		}
		
		
		$db=DB_Functions_Blocks::db_connect();
		
		
		if (empty($p_prename) || $p_prename == "*") {
			$p_prename = "##all";
		}else{
			$p_prename = '%' . str_replace('*','%',$p_prename) . '%';
		}
			
			
		if (empty($p_surname) || $p_surname == "*") {
			$p_surname = "##all";
		}else{
			$p_surname = '%' . str_replace('*','%',$p_surname) . '%';
		}
		if (empty($p_email) || $p_email == "*") {
			$p_email = "##all";
		}else{
			$p_email = '%' . str_replace('*','%',$p_email) . '%';
		}
		if (!isset($p_status)) $p_status = -2;


		$result = $db->query(
		
			"SELECT b.block_id,
					s.email,
					Concat(
						s.prename,
						' ',
						s.surname
					) as name,
					b.size,
					Concat(
						b.consumption_count,
						' / ',
						b.size
					) consumption,
					b.consumption_status,
					b.status,
					b.pay_status
			   from as_blocks b
			   inner join as_students s on s.student_id = b. student_id ");
			   
			   /*			  WHERE ('$p_prename' = '##all'
			  			OR s.prename LIKE '$p_prename')
			    AND ('$p_surname' = '##all'
			  			OR s.surname LIKE '$p_surname')
			    AND ('$p_email' = '##all'
			  			OR s.email LIKE '$p_email')
			    AND ($p_status = -2
			  			OR s.status = $p_status)
		      ORDER BY s.mod_dat DESC
		      LIMIT 2000;"); */
	
		
		
		if(!$result) echo $db->error;	
		
		echo "<div class='blocks-list-table'><table>\n
		      <tr>";
		
		echo     "<th style='min-width: 210px;'>Name</th>"
			   . "<th style='min-width: 210px;'>E-Mail</th>"
			   . "<th style='min-width: 110px;'>Verbrauchtstatus</th>"
			   . "<th style='min-width: 110px;'>Zahlstatus</th>"
			   . "<th style='min-width: 110px;'>Status</th>"
			   . "</tr>\n";



		while($line = $result->fetch_array()) {
		
			switch ($line["status"]) {
				case 1:  // aktiviert
					$line["status"] = "✔";
					break;
				case 0:  // deaktiviert
					$line["status"] = "✖";
					break;
				default:
					$line["status"] = "?";
					break;
			}		
			switch ($line["pay_status"]) {
				case 1:  // aktiviert
					$line["pay_status"] = "✔";
					break;
				case 0:  // deaktiviert
					$line["pay_status"] = "✖";
					break;
				default:
					$line["pay_status"] = "?";
					break;
			}
		
		
			echo "<tr block_id='" . htmlspecialchars($line["block_id"]) . "'>";
			
			//if ($p_only_last_modified) echo "<td><div>" . htmlspecialchars($line["mod_dat1"]) . "</div></td>";
			
			
			echo "    <td><div class='td-overflow'>" . htmlspecialchars($line["name"]) . "</div></td>"	
				   . "<td><div class='td-overflow'>" . htmlspecialchars($line["email"]) . "</div></td>"	
				   . "<td><div class='td-center'>" . htmlspecialchars($line["consumption"]) . "</div></td>"	
				   . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["pay_status"] . "</div></td>"
				   . "<td class='td-center' style='font-size: 20px; font-weight: bold'><div>" . $line["status"] . "</div></td>"
				   . "</tr>\n";
		
		}
		echo "</table></div>";
		
		

		if($result->num_rows === 0) echo '<br>Keine Blöcke gefunden, bitte andere Suchoptionen wählen.</b>';
	
		$db->close();

	}

	public function db_load_block_values_from_id($p_id){
	

			$db=$this->db_connect();
			$result = $db->query(
			
				"SELECT s.prename,
						s.surname,
						s.email,
						b.size,
						b.pay_status,
						b.consumption_status,
						b.consumption_count,
						b.status,
						b.remark
				   from as_blocks b
				   inner join as_students s on b.student_id = s.student_id
			      where block_id = " . $p_id . ";");
				  			
			if(!$result) echo $db->error;	
			

			$line = $result->fetch_array();
			
			if($result->num_rows === 0) {
				echo '<br>Kein Block gefunden.';
				$db->close();
  		   		return;	
			}else{
							
				$_SESSION["block_prename"] =  $line["prename"];
				$_SESSION["block_surname"] =  $line["surname"];
				$_SESSION["block_email"] =  $line["email"];
				$_SESSION["block_size"] =  $line["size"];
				$_SESSION["block_pay_status"] =  $line["pay_status"];
				$_SESSION["block_consumption_status"] =  $line["consumption_status"];
				$_SESSION["block_consumption_count"] =  $line["consumption_count"];
				$_SESSION["block_status"] =  $line["status"];
				$_SESSION["block_remark"] =  $line["remark"];
			}
			$db->close();
		
	}


	public function db_update_block( 	  $p_id,
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

		$_SESSION["block_error_msg"] = "";
		if(empty($p_prename)) {
			$_SESSION["block_error_msg"] .= "Bitte einen Vornamen eingeben.<br>";
		}else{
			$p_prename = "'" . $p_prename . "'";
		}
		if(empty($p_surname)) {
			$_SESSION["block_error_msg"] .= "Bitte einen Nachnamen eingeben.<br>";
		}else{
			$p_surname = "'" . $p_surname . "'";
		}
		if(empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
			$_SESSION["block_error_msg"] .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
		}else{
			$p_email = "'" . $p_email . "'";
		}
		if(empty($p_status)) $p_status = '1';
		
		if($p_status == 3) {
			
			$check = $this->db_get_student_with_email($p_merged_to);
			
			if(!(isset($check["status"]) && $check["status"] == 1)) {
				$_SESSION["block_error_msg"] .= "Bitte für die Fusionierung eine E-Mail- Adresse zu einem aktivierten Teilnehmer angeben.<br>";
				$p_merged_to = "''";
			}else{
				$p_merged_to = "'" . $p_merged_to . "'";
			}
		}else $p_merged_to = "''";
		if(empty($p_student_remark)) {
			$p_student_remark = "''";
		}else{
			$p_student_remark = "'" . $p_student_remark . "'";
		}
		
		$db=$this->db_connect();
		
		$statement = "
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
		$_SESSION["block_success_msg"] = false;
		if(empty($_SESSION["block_error_msg"])) {
			$result = $db->query($statement);
			if(!$result) {
				$_SESSION["block_error_msg"] = $db->error;
				$db->close();
				return false;
			}else {
				$_SESSION["block_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
				
				$db->close();
				return true;
			}
		}
	
	}

	public function db_get_student_with_email ($p_email) {
		
		$db=$this->db_connect();
		$result = $db->query(
			'SELECT student_id,
					prename,
					surname,
					status,
					email
			   FROM as_students
			  WHERE email = "' . $p_email . '";');
		
		if(!$result) echo $db->error;
		
		if($result->num_rows === 0) {
			
			$db->close();
			return false;
		}		
		$line = $result->fetch_array();
		$db->close();	
		return array('student_id'	=> $line['student_id'],
					 'email'		=> $line['email'],
					 'prename'		=> $line['prename'],
					 'surname'		=> $line['surname'],
			 		 'status'		=> $line['status']);		
	}

	public function db_insert_new_block  ($p_email,
										  $p_size,
										  $p_pay_status)
	{
		
		$_SESSION["block_error_msg"] = '';
		
		$student_data = $this->db_get_student_with_email($p_email);
		
		if(!$student_data) {
			$_SESSION["block_error_msg"] .= "Die E-Mail- Adresse konnte nicht gefunden werden.<br>";
			$_SESSION["block_prename"] = '';
			$_SESSION["block_surname"] = '';
		}else {
			$_SESSION["block_prename"] = $student_data['prename'];
			$_SESSION["block_surname"] = $student_data['surname'];
			$student_id = $student_data['student_id'];
		}
		
		if(!isset($p_size) || $p_size != 20) $p_size = 10;
		if(!isset($p_pay_status) || $p_pay_status != 1) $p_pay_status = 0;		
		
		$db=$this->db_connect();
		
		$result = false;
		$_SESSION["block_success_msg"] = false;
		if(empty($_SESSION["block_error_msg"])) {
						
	$statement = 		
		"
			INSERT INTO as_blocks( student_id,
								   size,
								   pay_status,
								   status)
								   
			VALUES 				  ($student_id,
								   $p_size,
								   $p_pay_status,
								   1);";		
			
			$result = $db->query($statement);
			if(!$result) {
				$_SESSION["block_error_msg"] = $db->error . $statement;
				$db->close();
				return false;
			}else {
				$_SESSION["block_id"]= $db->insert_id;
				$_SESSION["block_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
				
				$db->close();
				return true;
			}
		}
	}

	
}


$db_functions_ext = new DB_Functions_Ext();

?>