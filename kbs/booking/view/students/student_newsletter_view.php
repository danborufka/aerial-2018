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
	<link href="./css/student_newsletter_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=category_list'; // todo
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php; ?>
			<div id="container">
				<div id="title">
					<h2>Newsletter- Adressen</h2>
				</div>
	
				<div id="button-bar">
					<div id="button-back" class="rb-button">
						Zurück
					</div>		
					<div id="button-select-all" class="rb-button">
						Alles markieren
					</div>	
					
				</div>
	
				<form class="input-container clear-fix">
				
				
					<?php
					
					if(isset($_SESSION["premode"]) && $_SESSION["premode"] == "loading") {
						$newsletter_addresses = "E-Mail- Adressen werden geladen...";?>					
						<div style="font-size: 20px; margin-top: 20px" ><?php echo $newsletter_addresses;?></div>
					<?php
						
					}else{
						$newsletter_addresses = $db_functions->students->db_generate_newsletter_addresses(); ?>						
						<textarea id="s_newsletter_adresses" name="s_newsletter_adresses" rows="45"><?php echo $newsletter_addresses;?></textarea>
					<?php
					}
					?>
					
				</form>
				
			</div>
		</div>
	</body>
	<script type="text/javascript">
	$(document).ready(function() {
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, label, textarea, a").addClass("loading");
		};
		 
		 
		 $("#button-back" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=main_menu';

		});
		
		
		$( "#button-select-all" ).click(function() {
			document.getElementById("s_newsletter_adresses").select();
		});
	
	});
			<?php
				
			if(isset($_SESSION["premode"]) && $_SESSION["premode"] == "loading") {
				unset($_SESSION["premode"]);
				
			?>
				
				$("#button-back" ).click(function() {
					window.location.href = '<?php echo $rb_path_self?>?view=main_menu';
		
				});
				$("#rb-container, .rb-button, label, textarea, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?view=student_newsletter';
			<?php
			}
			?>
	

	</script>
</html>