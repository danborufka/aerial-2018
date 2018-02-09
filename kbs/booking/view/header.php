	<div id="header-line" class="clearfix">
		<div id="header-left">
			<h1>
				<?php global $rb_configuration;
				echo $rb_configuration->title_of_web_application_backend?></h1>
		</div>
		<?php
		
		if (!isset($rb_path_self)) $rb_path_self = str_replace("index.php", "", $_SERVER['PHP_SELF']);
		
		
		
		?> <div id="header-right"> <?php
		if (isset($_SESSION["login"]) && $_SESSION["login"] == "ok"
					&& !(isset($_GET["w1"]) && $_GET["w1"] == "small")
		) {
		?>
				<div class="header-right-button">
					<a href="<?php echo $rb_path_self?>?action=logout">Ausloggen</a>
				</div>
				
				
				<?php
				if (!isset($_SESSION["view"]) || $_SESSION["view"] != "main_menu")
				{ ?>
					<div class="header-right-button">
						<a href="<?php echo $rb_path_self?>?view=main_menu">Menü</a>
					</div>
				<?php
				} ?>
				<div id="header-right-user">
					<p>Angemeldet als: <?php echo $_SESSION["user_name"] ?></p>
				</div>
			
		<?php	
		} ?>
			</div>
	</div>
	<? if(isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] != 1) { ?>
		<div style='font-size: 15px; color: white; font-weight:bold' id="div640">TESTINSTANZ - TESTINSTANZ - TESTINSTANZ</div>
	<? } ?>
