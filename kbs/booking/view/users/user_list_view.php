<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title><?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/user_list_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?=$rb_path_self?>?view=main_menu';				
			});
		});
	</script>
</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php; ?>
		<div id="container">
			<div id="title">
				<h2>Trainer und User</h2>
			</div>
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
			<div id="button-bar">
				<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
					<div id="button-close" class="rb-button">
						Schließen
					</div>
				<?php }else { ?>
					<div id="button-go-back" class="rb-button">
						Zurück
					</div>
				<?php } ?>
				<div id="button-user-detail" class="rb-button">
					Details
				</div>
				<div id="button-create-user" class="rb-button">
					Neu
				</div>
			</div>


			<?php /* ******  BUTTON BAR END ****** */
			

				
				
				
			      /* ******  INIT FILTER VALUES BEGIN  ****** */

				if(!isset($_SESSION["u_filter_prename"])) {
					$_SESSION["u_filter_prename"] = "";
				}
				if(!isset($_SESSION["u_filter_surname"])) {
					$_SESSION["u_filter_surname"] = "";
				}
				if(!isset($_SESSION["u_filter_login_name"])) {
					$_SESSION["u_filter_login_name"] = "";
				}
				if(!isset($_SESSION["u_filter_status"]) ) {
					$_SESSION["u_filter_status"] = "1";
				}
				if(!isset($_SESSION["u_filter_search_code"]) ) {
					$_SESSION["u_filter_search_code"] = "";
				}
				if(!isset($_SESSION["u_filter_limit"]) ) {
					$_SESSION["u_filter_limit"] = "1000";
				}
				if(!isset($_SESSION["u_filter_only_last_modified"]) ) {
					$_SESSION["u_filter_only_last_modified"] = false;
				}
				
			     /* ******  INIT FILTER VALUES END  ****** */
			     
			     /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
			<div id="search-option-container">	
				<form id="form1" method="POST" action="process.php">
					<div id="search-option-row1">
						<label for='u_filter_prename'>Vorname:</label>
						<input type="text" id="u_filter_prename" name="u_filter_prename"
										value="<?php echo $_SESSION["u_filter_prename"]?>">
						<label for='u_filter_surname'>Nachname:</label>
						<input type="text" id="u_filter_surname" name="u_filter_surname"
										value="<?php echo $_SESSION['u_filter_surname']?>">
						<button type="submit" name= "action" value= "u_reset_user_filter_options" id="clear-button" class="rb-button" >Werte zurücksetzen</button>
					</div>
					<div id="search-option-row2">
						<label for='u_filter_login_name'>Login Name:</label>
						<input type="text" id="u_filter_login_name" name="u_filter_login_name" placeholder="Login oder Email"
										value="<?php echo $_SESSION['u_filter_login_name']?>">
						<label for="u_filter_status">Status*:</label>
						
						<select name='u_filter_status' id='u_filter_status' class='select-with-symbols'>
							<option value='1'<?php echo ($_SESSION["u_filter_status"] == 1  )? " selected":"" ?>>✔   aktiv</option>
							<option value='0'<?php echo ($_SESSION["u_filter_status"] == 0  )? " selected":"" ?>>✖   deaktiviert</option>
							<option value='-2'<?php echo ($_SESSION["u_filter_status"] == -2)? " selected":"" ?>>Ⓐ  alle</option>
						</select>
						
						<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						
							<input name="w1" type="hidden" value="small">
							
						<?php }?>
						
					<input type="submit" id="search-button" class="rb-button" value="Suchoptionen anwenden">
					</div>
				</form>
				
			    <?php  /* ******  SEARCH OPTION FORM END  ****** */ ?>
			</div>
			
			<?php  	   /* ******  USERS TABLE BEGIN  ****** */
		
		
		
			$db_functions->users->db_load_table_users($_SESSION["u_filter_prename"], $_SESSION["u_filter_surname"], $_SESSION["u_filter_login_name"],
												$_SESSION["u_filter_status"]);
			
			
			
					   /* ******  USERS	 TABLE END  ****** */    ?>

		</div>
	</div>
</body>
	
<script type="text/javascript">
	$(document).ready(function() {
		

		function rb_init_selectable(p_element) {			
			
			$(p_element + ' tr').addClass('rb-selectable');			
			$('.rb-selectable:not(:first-child)').on('click mousedown', function () {
				
				$('.ui-selected').toggleClass('ui-selected');
				$('.ui-selecting').toggleClass('ui-selecting');				
				$(this).addClass('ui-selected ui-selecting');				
				
			});
			
		};
		
		var alt_key_is_pressed = false;
		$(window).keydown(function(event) {
			alt_key_is_pressed = true;
		});
		$(window).keyup(function(event) {
			alt_key_is_pressed = false;
		});
				
	
		function rb_get_selected_id(){
			return $(".ui-selected, ui-selecting").first().attr('user_id');
		};	
	
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************

		rb_init_selectable(".table-users");
		
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
		} ?>
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		$( ".table-users  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick and taphold
			if(alt_key_is_pressed) return false;
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=user_detail_reload&user=' + ($(this).attr('user_id')) + expand_get_string;
		});
		$( "#button-user-detail" ).click(function() {
			var user_id = rb_get_selected_id();
			if(!user_id) {
				alert("Bitte zunächst einen User auswählen oder doppelklicken.");
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=user_detail_reload&user=' + (user_id) + expand_get_string;
			}
		});
		$( "#button-close").click(function() {
			window.close();			
		});
		$( "#button-create-user" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=user_new&action=reset_user_values' + expand_get_string;				
		});
		
		
		<?php		 /* ******  CLICK ACTION END  ****** */    ?>
		


		$( ".email-text" ).click(function() {   //    Text markieren
			var doc = document;
		    var text = this;    
		
		    if (doc.body.createTextRange) { // ms
		        var range = doc.body.createTextRange();
		        range.moveToElementText(text);
		        range.select();
		    } else if (window.getSelection) { // moz, opera, webkit
		        var selection = window.getSelection();            
		        var range = doc.createRange();
		        range.selectNodeContents(text);
		        selection.removeAllRanges();
		        selection.addRange(range);
		    }
		});
		
		
		$(function() {
		    $("form input, form select").keypress(function (e) {
		        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
		            $('#search-button').click();
		            return false;
		        } else {
		            return true;
		        }
		    });
		});		
		

	});

</script>

</html>