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
	<link href="./css/category_list_view.css" rel="stylesheet" type="text/css" />
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
				<h2>Kategorien</h2>
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
				<div id="button-category-detail" class="rb-button">
					Details
				</div>
				<div id="button-create-category" class="rb-button">
					Neu
				</div>
			</div>

			<?php /* ******  BUTTON BAR END ****** */
			
			      /* ******  INIT FILTER VALUES BEGIN  ****** */

				if(!isset($_SESSION["ca_filter_name"])) {
					$_SESSION["ca_filter_name"] = "";
				}
				if(!isset($_SESSION["ca_filter_status"])) {
					$_SESSION["ca_filter_status"] = "1";
				}
			     /* ******  INIT FILTER VALUES END  ****** */
			     

			     
			     /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
			<div id="search-option-container">	
				<form id="form1" method="POST" action="process.php">
					
					<div id="search-option-row1">
					
						<label for='ca_filter_name'>Kategoriename:</label>
						<input type="text" id="ca_filter_name" name="ca_filter_name"
										value="<?php echo $_SESSION['ca_filter_name']?>">
						<label for="ca_filter_status">Status:</label>
						
						<select name='ca_filter_status' id='ca_filter_status' class='select-with-symbols'>
							<option value='1'<?php echo ($_SESSION["ca_filter_status"] == 1  )? " selected":"" ?>>✔   aktiv</option>
							<option value='0'<?php echo ($_SESSION["ca_filter_status"] == 0  )? " selected":"" ?>>✖   deaktiviert</option>
							<option value='-2'<?php echo ($_SESSION["ca_filter_status"] == -2)? " selected":"" ?>>Ⓐ  alle</option>
						</select>
						
						
						
						<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						
							<input name="w1" type="hidden" value="small">
							
						<?php }?>
					<input name="view" type="hidden" value="category_list">
					<input type="submit" id="search-button" class="rb-button" value="Suchoptionen anwenden">
					</div>
				</form>
				
			    <?php  /* ******  SEARCH OPTION FORM END  ****** */ ?>
			</div>
			
			  	 <?  /* ******  CATEGORIES TABLE BEGIN  ****** */ 
		
			$db_functions->categories->db_load_table_categories($_SESSION["ca_filter_name"], $_SESSION["ca_filter_status"]);
				   /* ******  CATEGORIES	 TABLE END  ****** */    ?>

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
			return $(".ui-selected, ui-selecting").first().attr('cat_id');
		};	
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		
		rb_init_selectable(".table-categories");
		
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
			
		} ?>
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		$( ".table-categories  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick and taphold
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=category_detail_reload&cat=' + ($(this).attr('cat_id')) + expand_get_string;
		});


		$( "#button-category-detail" ).click(function() {
			var category_id = rb_get_selected_id();
			if(!category_id) {
				alert("Bitte eine Kategorie auswählen oder doppelklicken.");
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=category_detail_reload&cat=' + (category_id) + expand_get_string;
			}
		});
		
		$( "#button-close").click(function() {
			window.close();			
		});
		$( "#button-create-category" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=category_new&action=reset_category_values' + expand_get_string;				
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