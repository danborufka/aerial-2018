<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title><?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/location_list_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=main_menu';
			});
		});
	</script>
</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php; ?>
		<div id="container">
			<div id="title">
				<h2>Standorte</h2>
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
				<div id="button-location-detail" class="rb-button">
					Details
				</div>
				<div id="button-create-location" class="rb-button">
					Neu
				</div>
			</div>

			<?php /* ******  BUTTON BAR END ****** */
			

				
				
				
			      /* ******  INIT FILTER VALUES BEGIN  ****** */

				if(!isset($_SESSION["l_filter_name"])) {
					$_SESSION["l_filter_name"] = "";
				}
				if(!isset($_SESSION["l_filter_status"])) {
					$_SESSION["l_filter_status"] = "1";
				}
			     /* ******  INIT FILTER VALUES END  ****** */
			     
			     /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
			<div id="search-option-container">	
				<form id="form1" method="POST" action="process.php">
					<div id="search-option-row1">
						<label for='l_filter_name'>Name:</label>
						<input type="text" id="l_filter_name" name="l_filter_name"
										value="<?php echo $_SESSION["l_filter_name"]?>">
						<label for="l_filter_status">Status*:</label>
						
						<select name='l_filter_status' id='l_filter_status' class='select-with-symbols'>
							<option value='1'<?php echo ($_SESSION["l_filter_status"] == 1  )? " selected":"" ?>>✔   aktiv</option>
							<option value='0'<?php echo ($_SESSION["l_filter_status"] == 0  )? " selected":"" ?>>✖   deaktiviert</option>
							<option value='-2'<?php echo ($_SESSION["l_filter_status"] == -2)? " selected":"" ?>>Ⓐ  alle</option>
						</select>
						
						<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						
							<input name="w1" type="hidden" value="small">
							
						<?php }?>
						
					<input type="submit" id="search-button" class="rb-button" value="Suchoptionen anwenden">
					</div>
				</form>
				
			    <?php  /* ******  SEARCH OPTION FORM END  ****** */ ?>
			</div>
			
			<?php  	   /* ******  locations TABLE BEGIN  ****** */
		
		
		
			$db_functions->locations->db_load_table_locations($_SESSION["l_filter_name"], $_SESSION["l_filter_status"]);
			
			
			
					   /* ******  locations	 TABLE END  ****** */    ?>

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
	
		function rb_get_selected_id(){
			return $(".ui-selected, ui-selecting").first().attr('location_id');
		};	
	
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************

		rb_init_selectable(".table-locations");
		
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
		} ?>
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		$( ".table-locations  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick and taphold
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=location_detail_reload&location=' + ($(this).attr('location_id')) + expand_get_string;
		});
		$( "#button-location-detail" ).click(function() {
			var location_id = rb_get_selected_id();
			if(!location_id) {
				alert("Bitte zunächst einen Standort auswählen oder doppelklicken.");
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=location_detail_reload&location=' + (location_id) + expand_get_string;
			}
		});
		
		$( "#button-close").click(function() {
			window.close();			
		});
		$( "#button-create-location" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=location_new&action=reset_location_values' + expand_get_string;				
		});
		
		
		<?php		 /* ******  CLICK ACTION END  ****** */    ?>
			
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