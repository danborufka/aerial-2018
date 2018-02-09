<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Aerial Silk Booking</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/category_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=category_list';
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "category_new":
					$title = "Neue Kategorie anlegen";
					break;
				case "category_detail":
					$title = "Kategorie- Details";
					break;
				default:
					$title = "View nicht gefunden - Fehler";
					break;
			}
		?>
			<div id="container">
				<div id="title">
					<h2><?php echo $title?></h2>
				</div>
	
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
				<div id="button-bar">
					
				<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
					<div id="button-close" class="rb-button">
						Schließen
					</div>
				<?php } ?>
					<div id="button-go-back" class="rb-button">
						Zurück
					</div>
					<?php
					if ($_SESSION["view"] == 'category_detail_disabled') {?>
							<div id="button-registrations" class="rb-button">
								# Subkategorien
							</div>
					<?php } ?>			
					
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["ca_name"])) {
						$_SESSION["ca_name"] = "";
					}
					if(!isset($_SESSION["ca_sort_no"])) {
						$_SESSION["ca_sort_no"] = "50";
					}
					if(!isset($_SESSION["ca_has_sub_cat"]) ) {
						$_SESSION["ca_has_sub_cat"] = "1";
					}
					if(!isset($_SESSION["ca_status"]) ) {
						$_SESSION["ca_status"] = "1";
					}
								
			       /* ******  INIT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "category_detail") {
						if(isset($_SESSION["ca_no_reload_data"]) && $_SESSION["ca_no_reload_data"] == "true")	{
							unset($_SESSION["ca_no_reload_data"]);
						}else{							
							$db_functions->categories->db_load_category_values_from_id($_SESSION["cat_id"]);  // SET VALUES FROM ID *****
						}
					}
					
				
			       /* ******  DATA BEGIN ****** */?>
				
				<form id="form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for="ca_name">Kategoriename*:</label>
								<input name="ca_name" type="text" id="ca_name" value="<?php echo $_SESSION["ca_name"] ?>">
							</div>
							<div class="input-row">
								<label for="ca_sort_no">Sortierung*:</label>
								<input name="ca_sort_no" type="text" id="ca_sort_no" value="<?php echo $_SESSION["ca_sort_no"] ?>">
							</div>

							<div class="input-row" style="display:none">
								<label for="ca_has_sub_cat">Kategorie-Typ*:</label>
								
								<select name='ca_has_sub_cat' id='ca_type'>
									<option value='1'<?php echo ($_SESSION["ca_has_sub_cat"] == '1')? " selected":"" ?>>fixe Unterkategorien</option>
									<option value='0'<?php echo ($_SESSION["ca_has_sub_cat"] == '0')? " selected":"" ?>>freies Textfeld</option>
								</select>
							</div>
							<div class="input-row">
								
								<label for="ca_status">Status*:</label>
								<select name='ca_status' id='ca_status' class='select-with-symbols'>
									<option value='1'<?php echo ($_SESSION["ca_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
									<option value='0'<?php echo ($_SESSION["ca_status"] == 0)? " selected":"" ?>>✖   deaktiviert</option>
								</select>
							</div>
						</div>
					</div>	
					<div id="button-bar2">
					<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						<input name="w1" type="hidden" value="small">
					<?php }?>
					<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
					<input name="ca_no_reload_data" type="hidden" value = "true">
			   <?php     /* ******  DATA END ****** */
			   
			   
			   
			    	     /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
					if ($_SESSION["view"] == 'category_detail')
					{ ?>	
						<input name="cat_id" type="hidden" value="<?php echo $_SESSION["cat_id"] ?>">
						<button form="form" type="submit" name="action"
								value="ca_update_category" class="rb-button">Speichern</button>
						<button form="form" type="submit" name="action"
								value="ca_update_category_back" class="rb-button">Speichern und zurück</button>
						<button form="form" type="submit" name="action"
								value="ca_update_category_new" class="rb-button">Speichern und neu</button>
					<?php
					}else{?>	
						<button form="form" type="submit" name="action"
								value="ca_insert_category" class="rb-button">Speichern</button>
						<button form="form" type="submit" name="action"
								value="ca_insert_category_back" class="rb-button">Speichern und Zurück</button>
						<button form="form" type="submit" name="action"
								value="ca_insert_category_new" class="rb-button">Speichern und neu</button>
					<?php
					} ?>
					</div>
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="ca-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["ca_success_msg"]) && $_SESSION["ca_success_msg"]) {
							
							echo $_SESSION["ca_success_msg"];
							$_SESSION["ca_success_msg"] = false;
							
						}
						if(isset($_SESSION["ca_error_msg"]) && $_SESSION["ca_error_msg"] ) {
							echo $_SESSION["ca_error_msg"];
							unset($_SESSION["ca_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
				
			</div>
		</div>
	</body>
	<script type="text/javascript">
	$(document).ready(function() {
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		 $( "#saved_done" ).fadeOut( 5000, function() {});
		 
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
			
		}       /* ******  CLICK ACTION BEGIN  ****** */   ?>
		 
		$( "#button-close").click(function() {
			window.close();
		});
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
	});

	</script>
</html>