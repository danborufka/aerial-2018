<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Aerial Silk Booking</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="JK Informatik">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css" rel="stylesheet" type="text/css" />
	<link href="./css/header.css" rel="stylesheet" type="text/css" />
	<link href="./css/membership_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script src="./_v2/js/clientManager.js"></script>
</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php;
			$view_name_begin = 'voucherrequest';
			$pre = 'cf';  // = entity pre abbreviation
		?>
		<div id="container">
			
			<div class="container-overview">
				<div class="title">
					<h2>Anträge OS Blöcke</h2>
				</div>
				<div id="button-bar">
					<div id="button-go-back" onclick="window.location.href ='<?=$rb_path_self?>?view=main_menu'" class="rb-button">Zurück</div>
				</div>
				<div class='rb-table'>
					Loading ...
				</div>
			</div>

		</div>
	</div>
</body>
		
<script type="text/javascript">
	$(function() {
		cm.VoucherRequests.getSearchResult(0);
	});
</script>

</html>