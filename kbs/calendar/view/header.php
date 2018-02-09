	<div id="header-line" class="clearfix">
		<div id="header-left">
			<h1>Aerial Silks Kalender</h1>
		</div>
		<?php
		
		if (!isset($rb_path_self)) $rb_path_self = str_replace("index.php", "", $_SERVER['PHP_SELF']);
		
		
		
		?> <div id="header-right"> <?
		if (isset($_SESSION["login"]) && $_SESSION["login"] == "ok"
					&& !(isset($_GET["w1"]) && $_GET["w1"] == "small")
		) {
		?>
				<div class="header-right-button">
					<a href="<?=str_replace("calendar", "booking", $rb_path_self)?>?action=logout">Ausloggen</a>
				</div>
				
				
				<div class="header-right-button">
					<a href="<?=str_replace("calendar", "booking", $rb_path_self)?>?view=main_menu">Menü</a>
				</div>
				
				<div id="header-right-user">
					<p>Angemeldet als: <?php echo $_SESSION["user_name"] ?></p>
				</div>
				
				
			
		<?php	
		} ?>
			<!--<a href="http://www.aerialsilks.at" target="_blank"><img id="img-logo" src="images/logo.png"></a>-->
			</div>
	</div>
	<? if(isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] != 1) { ?>
		<div style='font-size: 15px; color: white; font-weight:bold' id="div640">TESTINSTANZ - TESTINSTANZ - TESTINSTANZ</div>
	<? } ?>
