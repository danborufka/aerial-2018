<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>10er & 20er Blöcke - <?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author"   content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="./css/general.css"         rel="stylesheet" type="text/css" />
	<link href="./css/header.css"          rel="stylesheet" type="text/css" />
	<link href="./css/block_list_view.css" rel="stylesheet" type="text/css" />
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
				<h2>10er & 20er Blöcke</h2>
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
				<div id="button-block-detail" class="rb-button">
					Details
				</div>
				<div id="button-create-block" class="rb-button">
					Neu
				</div>
			</div>

			<?php /* ******  BUTTON BAR END ****** */
			

			      /* ******  INIT FILTER VALUES BEGIN  ****** */

				if(!isset($_SESSION["block_filter_prename"])) {
					$_SESSION["block_filter_prename"] = "";
				}
				if(!isset($_SESSION["block_filter_surname"])) {
					$_SESSION["block_filter_surname"] = "";
				}
				if(!isset($_SESSION["block_filter_email"])) {
					$_SESSION["block_filter_email"] = "";
				}
				if(!isset($_SESSION["block_filter_status"]) ) {
					$_SESSION["block_filter_status"] = "1";
				}
				if(!isset($_SESSION["block_filter_pay_status"]) ) {
					$_SESSION["block_filter_pay_status"] = "-1";
				}
				if(!isset($_SESSION["block_filter_consumption_status"]) ) {
					$_SESSION["block_filter_consumption_status"] = "-1";
				}
				
			     /* ******  INIT FILTER VALUES END  ****** */
			     
			     
			     
			     
			     /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
			<div id="search-option-container">	
				<form id="form1" method="POST" action="process.php">
					
					<div id="search-option-row1">
					
						<label for='block_filter_prename'>Vorname:</label>
						<input type="text" id="block_filter_prename" name="block_filter_prename"
										value="<?php echo $_SESSION["block_filter_prename"]?>">
					
						<label for='block_filter_surname'>Nachname:</label>
						<input type="text" id="block_filter_surname" name="block_filter_surname"
										value="<?php echo $_SESSION['block_filter_surname']?>">
					
						<label for='block_filter_email'>E-Mail:</label>
						<input type="text" id="block_filter_email" name="block_filter_email"
										value="<?php echo $_SESSION['block_filter_email']?>">
					
						<button type="submit" name= "action" value= "block_reset_filter_options" id="clear-button" class="rb-button" >Werte zurücksetzen</button>
						
					</div>
					<div id="search-option-row2">
					
						<label for="block_filter_consumption_status">Verbraucht:</label>						
						<select name='block_filter_consumption_status' id='block_filter_consumption_status'>
							<option value='-1'<?php echo ($_SESSION["block_filter_consumption_status"] == -1  )? " selected":"" ?>>egal</option>
							<option value='0'<?php echo ($_SESSION["block_filter_consumption_status"] == 0  )? " selected":"" ?>>nicht verbraucht</option>
							<option value='1'<?php echo ($_SESSION["block_filter_consumption_status"] == 1  )? " selected":"" ?>>alles aufgebraucht</option>
						</select>
						
						<label for="block_filter_pay_status">Zahlstatus:</label>						
						<select name='block_filter_pay_status' id='block_filter_pay_status'>
							<option value='-1'<?php echo ($_SESSION["block_filter_pay_status"] == -1  )? " selected":"" ?>>egal</option>
							<option value='0'<?php echo ($_SESSION["block_filter_pay_status"] == 0  )? " selected":"" ?>>offen</option>
							<option value='1'<?php echo ($_SESSION["block_filter_pay_status"] == 1  )? " selected":"" ?>>bezahlt</option>
						</select>
						
						<label for="block_filter_status">Status:</label>						
						<select name='block_filter_status' id='block_filter_status' class='select-with-symbols'>
							<option value='1'<?php echo ($_SESSION["block_filter_status"] == 1  )? " selected":"" ?>>✔   aktiv</option>
							<option value='0'<?php echo ($_SESSION["block_filter_status"] == 0  )? " selected":"" ?>>✖   storniert</option>
						</select>
						
						<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						
							<input name="w1" type="hidden" value="small">
							
						<?php }?>
						
					<input type="submit" id="search-button" class="rb-button" value="Suchoptionen anwenden">
					</div>
				</form>
				
			    <?php  /* ******  SEARCH OPTION FORM END  ****** */ ?>
			</div>
			
			<?php  	   /* ******  BLOCKS TABLE BEGIN  ****** */ ?>
		
		
			<div id="table-container">
				<?
				get_db_functions_ext()->blocks->db_load_table_blocks(	$_SESSION["block_filter_email"],
																		$_SESSION["block_filter_prename"],
																		$_SESSION["block_filter_surname"],
																		$_SESSION["block_filter_pay_status"],
																		$_SESSION["block_filter_consumption_status"],
																		$_SESSION["block_filter_status"]); ?>
			</div>


					  <? /* ******  BLOCKS	 TABLE END  ****** */    ?>
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
			return $(".ui-selected, ui-selecting").first().attr('block_id');
		};
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		
		rb_init_selectable(".blocks-list-table");
		
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			if(isset($_GET["c1"]) && is_numeric($_GET["c1"])) {
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "&c1=" . $_GET["c1"] . "';";
			}else{
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";				
			}
		}else{
			echo "expand_get_string = '';";
			
		} ?>
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		$( ".blocks-list-table  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick and taphold
			if(alt_key_is_pressed) return false;
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=block_detail_reload&block=' + ($(this).attr('block_id')) + expand_get_string;
		});

		$( "#button-block-detail" ).click(function() {
			var block_id = rb_get_selected_id();
			if(!block_id) {
				alert("Bitte einen Block auswählen.");
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=block_detail_reload&block=' + (block_id) + expand_get_string;
			}
		});
		
		$( "#button-close").click(function() {
			window.close();			
		});
		$( "#button-create-block" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=block_new&action=reset_block_values' + expand_get_string;				
		});
		
		
		<?php		 /* ******  CLICK ACTION END  ****** */    ?>
		

		function enable_email_selection(p_element) {  //    Text markieren
			var doc = document;
		    var text = p_element;    
		
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
		}

		$( ".email-text" ).click(function() {  
			enable_email_selection(this);
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

		$("select").on('change', function () {
			reload_table(0);
		}); 
		
		$("input[type='text']").on('keyup', function () {
			reload_table(400);
		}); 
		 

		var delayTimer;
		function reload_table(p_delay_in_ms) {
		    clearTimeout(delayTimer);
		    delayTimer = setTimeout(function(){
		    	var url = "controller/ajax_functions/reload_blocks.php";
		    	url = url + '?v=' + $("#block_filter_prename").val();
		    	url = url + '&n=' + $("#block_filter_surname").val();
		    	url = url + '&e=' + $("#block_filter_email").val();
		    	url = url + '&s=' + $("#block_filter_status").val();
		    	$( "#table-container" ).load(url, function() {
					$( ".block-list-table  tr:not(:first-child)" ).on('dblclick taphold', function() {  // doubleclick
						rb_set_loading_effect();
						window.location.href = '<?php echo $rb_path_self?>?view=block_detail_reload&block=' + ($(this).attr('block_id')) + expand_get_string;
					});
					rb_init_selectable(".block-list-table");
					$( ".email-text" ).click(function() {  
						enable_email_selection(this);
					});
				});
		    	
		    }, p_delay_in_ms);
		}

	});

</script>

</html>