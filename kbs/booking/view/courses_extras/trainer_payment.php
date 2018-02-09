<?php
defined("main-call") or die();
?>
<!DOCTYPE html>
<head>
    <title><?= $rb_configuration->title_of_web_application_backend ?></title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="JK Informatik Ilja Jochum>
    <meta http-equiv=" content-type
    " content="text/html; charset=utf-8" />
    <link href="./lib/jqueryui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="./css/general.css" rel="stylesheet" type="text/css"/>
    <link href="./css/header.css" rel="stylesheet" type="text/css"/>
    <link href="./css/attendance_view.css" rel="stylesheet" type="text/css"/>
    <link href="./css/trainer_payment_view.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <script src="./lib/jquery.js"></script>
    <script src="./lib/jqueryui/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#button-go-back").click(function () {
                $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
                window.location.href = '<?=$rb_path_self?>?view=main_menu';
            });
        });
    </script>
</head>
<body>
<div id="rb-container">
    <?php require $rb_configuration->relative_path_of_header_php; ?>
    <div id="container">
        <div id="title">
            <h2>Trainerabrechnung</h2>
        </div>
        <div id="button-bar">
            <div id="button-go-back" class="rb-button3">
                Zurück
            </div>
        </div>

        <form id="trainer-payment-form" method="POST" action="process.php">
            <button type="submit" name="action" value="p_trainer_payment" class="rb-button3">
                Speichern
            </button>
            <?$db_functions->courses_extras->db_load_table_trainer_payment(); ?>
            <div style="padding-top: 12px;">
                <button type="submit" name="action" value="p_trainer_payment" class="rb-button3">
                    Speichern
                </button>
            </div>
        </form>
    </div>
</div>
</body>


<script type="text/javascript">
    $(document).ready(function () {
        function rb_set_loading_effect() {
            $("#rb-container, .rb-button, .rb-button3, button, table, label, select, input, a").addClass("loading");
        }

        $("form").submit(function () {
            rb_set_loading_effect();
        });
    });

</script>

</html>