<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Aerial Silk Booking</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/subcategory_list_view.css" rel="stylesheet" type="text/css" />
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
				<h2>Unterkategorien</h2>
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
				<div id="button-subcategory-detail" class="rb-button">
					Details
				</div>
				<div id="button-create-subcategory" class="rb-button">
					Neu
				</div>
			</div>

			<?php /* ******  BUTTON BAR END ****** */
			
			      /* ******  INIT FILTER VALUES BEGIN  ****** */

				if(!isset($_SESSION["sca_filter_categories"])) {
					$_SESSION["sca_filter_categories"] = "1";
				}
				if(!isset($_SESSION["sca_filter_status"])) {
					$_SESSION["sca_filter_status"] = "1";
				}
			     /* ******  INIT FILTER VALUES END  ****** */
			     

			     
			     /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
			<div id="search-option-container">	
				<form id="form1" method="POST" action="process.php">
					
					<div id="search-option-row1">
					
						<label for='sca_filter_category'>Kategorie:</label>
						<select name='sca_filter_categories' id='sca_filter_categories'>";
							<?php $db_functions->select_options->db_get_category_select_options($_SESSION["sca_filter_categories"], false); ?>
						</select>
						<label for="sca_filter_status">Status:</label>
						
						<select name='sca_filter_status' id='sca_filter_status' class='select-with-symbols'>
							<option value='1'<?php echo ($_SESSION["sca_filter_status"] == 1  )? " selected":"" ?>>✔   aktiv</option>
							<option value='0'<?php echo ($_SESSION["sca_filter_status"] == 0  )? " selected":"" ?>>✖   deaktiviert</option>
							<option value='-2'<?php echo ($_SESSION["sca_filter_status"] == -2)? " selected":"" ?>>Ⓐ  alle</option>
						</select>
						
						
						
						<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						
							<input name="w1" type="hidden" value="small">
							
						<?php }?>
						
					<input name="view" type="hidden" value="subcategory_list">
					<input type="submit" id="search-button" class="rb-button" value="Suchoptionen anwenden">
					</div>
				</form>
				
			    <?php  /* ******  SEARCH OPTION FORM END  ****** */ ?>
			</div>
			
			  	 <?  /* ******  SUBCATEGORIES TABLE BEGIN  ****** */ 
		
			$db_functions->subcategories->db_load_table_subcategories($_SESSION["sca_filter_categories"], $_SESSION["sca_filter_status"]);
				   /* ******  SUBCATEGORIES	 TABLE END  ****** */    ?>

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
			return $(".ui-selected, ui-selecting").first().attr('subcat_id');
		};	
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		
		rb_init_selectable(".table-subcategories");
		
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
			
		} ?>
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		$( ".table-subcategories  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick and taphold
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=subcategory_detail_reload&subcat=' + ($(this).attr('subcat_id')) + expand_get_string;
		});


		$( "#button-subcategory-detail" ).click(function() {
			var subcategory_id = rb_get_selected_id();
			if(!subcategory_id) {
				alert("Bitte eine Unterkategorie auswählen oder doppelklicken.");
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=subcategory_detail_reload&subcat=' + (subcategory_id) + expand_get_string;
			}
		});
		
		$( "#button-close").click(function() {
			window.close();			
		});
		$( "#button-create-subcategory" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=subcategory_new&action=reset_subcategory_values' + expand_get_string;				
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