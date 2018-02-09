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
	<link rel="stylesheet" href="./css/general.css" type="text/css" />
	<link rel="stylesheet" href="./css/header.css" type="text/css" />
	<link rel="stylesheet" href="./css/main_menu_view.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	</head>
<body>
	<div id="super-container" class="clearfix">
		<?php require "header.php";
			$db_functions->extras->db_load_welcome_msg(); // load $_SESSION["m_welcome_msg"]
		?>
		<div id="container-main-menu">
			<div id="main-box">
				<form id="welcome-form" method="POST" action="process.php">
					<div id="welcome-message">
						<? echo nl2br($_SESSION["m_welcome_msg"]); ?>
					</div>
					<textarea id="welcome-textarea" name="m_welcome_msg" style="display:none; width: 100%; min-height: 300px; height: auto;"><? echo $_SESSION["m_welcome_msg"]; ?></textarea>
					<div id="msg-read-more" class="rb-msg">
						<a href="#" >Weiterlesen</a>
					</div>
					<? if($_SESSION["user_is_admin"] == 1 || $_SESSION["user_is_organizer"] == 1) { ?>
					<div id="msg-change" style="display:none" class="rb-msg">
						<a href="#" >Ändern</a>
					</div>
					<div id="msg-save" style="display:none" class="rb-msg">
						<a href="#" >Speichern</a>
					</div>
					<? } ?>
				</form>
				<div id="main-title">
					<h2>Menü</h2>
				</div>				
				<ul>
					<li><a href="<?php echo $rb_path_self?>?view=course_list&view_mode=full">Kurse</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=course_list&view_mode=simple">Kurse im Einfachmodus</a></li>
					
				<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
					<li><a href="<?php echo $rb_path_self?>?view=student_list_v2">Teilnehmer</a></li>

					
					<? if($_SESSION["user_is_admin"] == 1) { ?>
					<li><a href="<?php echo $rb_path_self?>?view=membership">Anträge Mitgliedschaften</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=voucherrequest">Anträge OS Block</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=trainer_payment">Trainerabrechnung</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=user_list">Trainer und User</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=course_format">Kursformate</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=course_type">Kursart</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=course_level">Kursebene</a></li>
<!--					<li><a href="--><?php //echo $rb_path_self?><!--?view=category_list">Kategorien</a></li>-->
<!--					<li><a href="--><?php //echo $rb_path_self?><!--?view=subcategory_list">Unterkategorien</a></li>-->
					<li><a href="<?php echo $rb_path_self?>?view=location_list">Standorte</a></li>
					<li><a href="<?php echo $rb_path_self?>?view=student_newsletter&premode=loading">Newsletter- Adressen</a></li>
					<? } ?>
					<li id="button-notes"><a href="#">Verwaltungsnotizen</a></li>
				<? } ?>
					<li id="button-calendar"><a href="#">Kalender</a></li>
					<li><a href="<?php echo $rb_path_self?>?action=logout">Ausloggen</a></li>
				</ul>
			</div>
<!--			<div id="developed-by"><a href="http://www.breitschopf.wien" target="_blank">Developed by Breitschopf IT Solutions</a></div>-->
		</div>
	</div>
	
	<script src="./lib/jquery.js"></script>
	<script type="text/javascript">
			
		function rb_set_loading_effect() {
				$("#super-container, #container, div, table, label, select, input, a").addClass("loading");
		};
		$("#msg-read-more a").click(function() {
			$("#welcome-message").css("max-height","none");
			$("#msg-read-more").css("display","none");
			<? if($_SESSION["user_is_admin"] == 1 || $_SESSION["user_is_organizer"] == 1) { ?>
			$('#msg-change').css("display","inline-block");
			<? } ?>
		});
		<? if($_SESSION["user_is_admin"] == 1 || $_SESSION["user_is_organizer"] == 1) { ?>
		$("#msg-change a").click(function() {
			$("#msg-change, #welcome-message").css("display","none");
			$('#msg-save').css("display","inline-block");
			$('#welcome-textarea').css("display","block");
		});
		
		$("#msg-save a").click(function() {
			rb_set_loading_effect();
	        $('#welcome-form').append("<input type='hidden' name='action' value='m_update_welcome_msg'/>");
	        $('#welcome-form').submit(); 
			
		});
		<? } ?>
		
		
		
		var DELAY = 300, clicks = 0, timer = null;

	    $("#button-notes").on("click", function(e){
	
	        clicks++;  //count clicks
	
	        if(clicks === 1) {
	
	            timer = setTimeout(function() {
	 
	                clicks = 0;             //after action performed, reset counter
					window.location.href = "<?php echo $rb_path_self?>?view=note_list";
	
	            }, DELAY);
	
	        } else {
	
	            clearTimeout(timer);    //prevent single-click action
	            clicks = 0;             //after action performed, reset counter
	            window.open('<?php echo $rb_path_self?>?view=note_list','NewWin','width=1600,height=900');
	        }
	
	    })
	    .on("dblclick", function(e){
	        e.preventDefault();  //cancel system double-click event
	    });		
		
	    $("#button-calendar").on("click", function(e){
	
	        clicks++;  //count clicks
	
	        if(clicks === 1) {
	
	            timer = setTimeout(function() {
	 
	                clicks = 0;             //after action performed, reset counter
					window.location.href = "<?php echo str_replace("booking", "calendar", $rb_path_self)?>?cal_view=month_view";
	
	            }, DELAY);
	
	        } else {
	
	            clearTimeout(timer);    //prevent single-click action
	            clicks = 0;             //after action performed, reset counter
	            window.open('<?php echo str_replace("booking", "calendar", $rb_path_self)?>?cal_view=month_view','NewWin','width=1600,height=900');
	        }
	
	    })
	    .on("dblclick", function(e){
	        e.preventDefault();  //cancel system double-click event
	    });
			
	</script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
	
		$(document).ready(function() {
				
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "./lib/jqueryui/jquery-ui.css"
			}).prependTo("head");
		
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "./css/course_list_view.css"
			}).prependTo("head");
		
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "./css/simple_course_list_view.css"
			}).prependTo("head");
		
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "../calendar/css/general.css"
			}).prependTo("head");
		
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "../calendar/css/header.css"
			}).prependTo("head");
		
			$("<link/>", {
			   rel: "stylesheet",
			   type: "text/css",
			   href: "../calendar/css/month_view.css"
			}).prependTo("head");
		
		});
	
	</script>
	<div style="display: none;">
		<img src="lib/jqueryui/images/ui-bg_flat_75_e5daf2_40x100.png">
	</div>
</body>
</html>