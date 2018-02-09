<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
    <title><?= $rb_configuration->title_of_web_application_backend ?></title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="Ing. Roman Breitschopf, BA">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="./lib/jqueryui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="./css/general.css" rel="stylesheet" type="text/css"/>
    <link href="./css/header.css" rel="stylesheet" type="text/css"/>
    <link href="./css/course_list_view.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <script src="./lib/jquery.js"></script>
    <script src="./lib/jquery.mobile.min.js"></script>
    <script src="./lib/jqueryui/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#button-go-back").click(function () {
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
            <h2>Kurse</h2>
        </div>
        <?php /* ******  BUTTON BAR BEGIN ****** */ ?>
        <div id="button-bar">
            <div id="button-go-back" class="rb-button3">
                Zurück
            </div>
            <div id="button-simple-mode" class="rb-button3">
                Einfachmodus
            </div>
            <div id="button-attendance" class="rb-button3">
                Anwesenheitsliste
            </div>
            <div id="button-safety-check" class="rb-button3">
                Sicherheitsüberprüfung
            </div>
            <div id="button-course-notes" class="rb-button3">
                Kursnotizen
            </div>
            <div style="display: block">
                <?php if ($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
                    <div id="button-course-detail" class="rb-button3">
                        Kursdetails
                    </div>
                    <div id="button-registrations" class="rb-button3">
                        Anmeldungen
                    </div>
                    <div id="button-create-course" class="rb-button3">
                        Neu
                    </div>
                    <div id="button-publishing" class="rb-button3">
                        Alle veröffentlichen
                    </div>
                    <div id="button-last-modified" class="rb-button3">
                        Zuletzt geändert
                    </div>
                    <div id="button-open-tasks" class="rb-button3">
                        <span>Offene Aufgaben (<?= $db_functions->courses->db_count_todo() ?>)</span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php /* ******  BUTTON BAR END ****** */


        /* ******  INIT FILTER VALUES BEGIN  ****** */

        if (!isset($_SESSION["filter_trainer"]) ||
            $_SESSION["filter_trainer"] == "default"
        ) {
            if ($_SESSION["user_is_organizer"] == 1 ||
                $_SESSION["user_is_admin"] == 1
            ) {
                $_SESSION["filter_trainer"] = "all";
            } else {
                $_SESSION["filter_trainer"] = $_SESSION["user_id"];
            }
        }
        if (!isset($_SESSION["filter_from_date"]) ||
            $_SESSION["filter_from_date"] == "default" ||
            DateTime::createFromFormat('d.m.Y', $_SESSION["filter_from_date"]) == false
        ) {
            $_SESSION["filter_from_date"] = date('d.m.Y', strtotime('today - 3 month'));
        }
        if (!isset($_SESSION["filter_to_date"]) ||
            $_SESSION["filter_to_date"] == "default" ||
            DateTime::createFromFormat('d.m.Y', $_SESSION["filter_to_date"]) == false
        ) {
            $_SESSION["filter_to_date"] = date('d.m.Y', strtotime('today + 6 month'));
        }
        if (!isset($_SESSION["filter_location"]) ||
            $_SESSION["filter_location"] == "default"
        ) {
            $_SESSION["filter_location"] = "all";
        }
        if (!isset($_SESSION["filter_categories"]) ||
            $_SESSION["filter_categories"] == "default"
        ) {
            $_SESSION["filter_categories"] = "all";
        }
        if (!isset($_SESSION["filter_subcategories"]) ||
            $_SESSION["filter_subcategories"] == "default"
        ) {
            $_SESSION["filter_subcategories"] = "all";
        }
        if (!isset($_SESSION["filter_zeitraum"]) ||
            $_SESSION["filter_zeitraum"] == "default"
        ) {
            if ($_SESSION['user_id'] == 1) {
                $_SESSION["filter_zeitraum"] = "ab_heute";
            } else {
                $_SESSION["filter_zeitraum"] = "aktuell";
            }

        }
        if (!isset($_SESSION["filter_status"]) ||
            $_SESSION["filter_status"] == "default"
        ) {
            $_SESSION["filter_status"] = "-1";
        }
        if (!isset($_SESSION["filter_publishing"]) ||
            $_SESSION["filter_publishing"] == "default"
        ) {
            $_SESSION["filter_publishing"] = "-1";
        }
        if (!isset($_SESSION["filter_course_number"]) ||
            $_SESSION["filter_course_number"] == "default"
        ) {
            $_SESSION["filter_course_number"] = "";
        }
        if (!isset($_SESSION["filter_student_email"]) ||
            $_SESSION["filter_student_email"] == "default"
        ) {
            $_SESSION["filter_student_email"] = "";
        }
        if (!isset($_SESSION["filter_student_vorname"]) ||
            $_SESSION["filter_student_vorname"] == "default"
        ) {
            $_SESSION["filter_student_vorname"] = "";
        }
        if (!isset($_SESSION["filter_student_nachname"]) ||
            $_SESSION["filter_student_nachname"] == "default"
        ) {
            $_SESSION["filter_student_nachname"] = "";
        }
        if (!isset($_SESSION["filter_list_view_mode"]) ||
            $_SESSION["filter_list_view_mode"] == "default"
        ) {
            $_SESSION["filter_list_view_mode"] = 2;
        }
        if (!isset($_SESSION["filter_only_todo"]) ||
            $_SESSION["filter_only_todo"] == "default"
        ) {
            $_SESSION["filter_only_todo"] = 0;
        }
        if (!isset($_SESSION["filter_course_format"])) {
            $_SESSION["filter_course_format"] = 'all';
        }
        if (!isset($_SESSION["filter_course_type"])) {
            $_SESSION["filter_course_type"] = 'all';
        }
        if (!isset($_SESSION["filter_course_level"])) {
            $_SESSION["filter_course_level"] = 'all';
        }

        /* ******  INIT FILTER VALUES END  ****** */


        /* ******  SEARCH OPTION FORM BEGIN  ****** */ ?>
        <div id="search-option-container">
            <form name="form1" id="form1" method="POST">

                <div id="search-option-row1">

                    <label for='trainer'>Trainer:</label>
                    <select name='filter_trainer' id='filter_trainer'>
                        <?php $db_functions->select_options->db_get_trainer_select_options($_SESSION["filter_trainer"], true, false); ?>
                    </select>

                    <label for='filter_location'>Standort:</label>
                    <select name='filter_location' id='filter_location'>
                        <?php $db_functions->select_options->db_get_location_select_options($_SESSION["filter_location"], true, false); ?>
                    </select>


                    <label for="filter_status">Status:</label>
                    <select name='filter_status' id='filter_status' class='select-with-symbols'>


                        <option value='-1'<?php echo ($_SESSION["filter_status"] == -1) ? " selected" : "" ?>>
                            ✎&nbsp&nbsp&nbsp/&nbsp&nbsp&nbsp✔&nbsp&nbsp&nbsp/&nbsp&nbsp&nbsp✔✔
                        </option>
                        ";
                        <option value='1'<?php echo ($_SESSION["filter_status"] == 1) ? " selected" : "" ?>>✎ in
                            Bearbeitung
                        </option>
                        <option value='2'<?php echo ($_SESSION["filter_status"] == 2) ? " selected" : "" ?>>✔ aktiv
                        </option>
                        <option value='3'<?php echo ($_SESSION["filter_status"] == 3) ? " selected" : "" ?>>✔✔ bezahlt
                            (Bezahlung vollständig)
                        </option>
                        <option value='4'<?php echo ($_SESSION["filter_status"] == 4) ? " selected" : "" ?>>✔✔✔ erledigt
                            (bezahlt, Trainer entlohnt)
                        </option>
                        <option value='0'<?php echo ($_SESSION["filter_status"] == 0) ? " selected" : "" ?>>✖
                            deaktiviert
                        </option>
                        <option value='-2'<?php echo ($_SESSION["filter_status"] == -2) ? " selected" : "" ?>>Ⓐ alle
                        </option>
                    </select>
                    <label for='filter_course_number'>Kurs- Nr.:</label>
                    <input style="margin-left: 2px; margin-right: 19px;" type="text" name="filter_course_number"
                           id="filter_course_number" value="<?= $_SESSION["filter_course_number"] ?>">
                    <label for='filter_student_email'>E-Mail:</label>
                    <input style="margin-left: 2px; margin-right: 19px;" type="text" name="filter_student_email"
                           id="filter_student_email" placeholder="E-Mail-Adresse"
                           value="<?= $_SESSION["filter_student_email"] ?>">
                    <button type="submit" id="search-button" formaction="process.php" class="rb-button">Ansichtsoptionen
                        anwenden
                    </button>
                </div>
                <div id="search-option-row2">
                    <label for='zeitraum'>Zeitraum:</label>
                    <select name='filter_zeitraum' id='filter_zeitraum'>
                        <option value='aktuell'<?php echo ($_SESSION["filter_zeitraum"] == "aktuell") ? " selected" : "" ?>>
                            aktuell und zukünftig
                        </option>
                        <option value='ab_heute'<?php echo ($_SESSION["filter_zeitraum"] == "ab_heute") ? " selected" : "" ?>>
                            ab heute
                        </option>
                        <option value='von_bis'<?php echo ($_SESSION["filter_zeitraum"] == "von_bis") ? " selected" : "" ?>>
                            von bis
                        </option>
                        <option value='all'<?php echo ($_SESSION["filter_zeitraum"] == "all") ? " selected" : "" ?>>
                            uneingeschränkt
                        </option>
                    </select>
                    <label for="begin_date1" id="search-option-row2-from">von:</label>
                    <input name="filter_from_date" type="text" id="begin_date1"
                           value="<?php echo $_SESSION["filter_from_date"] ?>" readonly>
                    <label for="end_date1" id="search-option-row2-to">bis:</label>
                    <input name="filter_to_date" type="text" id="end_date1"
                           value="<?php echo $_SESSION["filter_to_date"] ?>" readonly>
                    <label for='filter_publishing'>Veröffentl.:</label>
                    <select name='filter_publishing' id='filter_publishing'>
                        <option value='-1'<?php echo ($_SESSION["filter_publishing"] == -1) ? " selected" : "" ?>>alle
                        </option>
                        <option value='1'<?php echo ($_SESSION["filter_publishing"] == 1) ? " selected" : "" ?>>✎ bereit
                            zur Veröffentlichung
                        </option>
                        <option value='2'<?php echo ($_SESSION["filter_publishing"] == 2) ? " selected" : "" ?>>✔
                            veröffentlicht solange Status aktiv
                        </option>
                        <option value='3'<?php echo ($_SESSION["filter_publishing"] == 3) ? " selected" : "" ?>>✖ nicht
                            veröffentlicht
                        </option>
                    </select>
                    <label for='filter_student_vorname'>Vorname:</label>
                    <input style="margin-left: 2px; margin-right: 19px;" type="text" name="filter_student_vorname"
                           id="filter_student_vorname" placeholder="Vorname"
                           value="<?= $_SESSION["filter_student_vorname"] ?>">
                    <div id="button-reset-options" id="clear-button" class="rb-button" style="width: 210px">
                        Optionen zurücksetzen
                    </div>
                </div>
                <div id="search-option-row3">
                    <input type='hidden' name='filter_categories' id='filter_categories' value='all'>
                    <input type='hidden' name='filter_subcategories' id='filter_subcategories' value='all'>
                    <input type='hidden' name='filter_list_view_mode' id='filter_list_view_mode' value='2'>

                    <label for='filter_course_format'>Kursformat:</label>
                    <select name='filter_course_format' id='filter_course_format'>
                        <?php $db_functions->select_options->db_get_course_format_select_options($_SESSION["filter_course_format"], true, false); ?>
                    </select>
                    <label for='filter_course_type'>Kursart:</label>
                    <select name='filter_course_type' id='filter_course_type'>
                        <?php $db_functions->select_options->db_get_course_type_select_options($_SESSION["filter_course_type"], true, false); ?>
                    </select>
                    <label for='filter_course_level'>Kursebene:</label>
                    <select name='filter_course_level' id='filter_course_level'>
                        <?php $db_functions->select_options->db_get_course_level_select_options($_SESSION["filter_course_level"], true, false); ?>
                    </select>
                    <label style="min-width:253px"></label>
                    <label for='filter_student_nachname'>Nachname:</label>
                    <input style="margin-left: 2px; margin-right: 19px;" type="text" name="filter_student_nachname"
                           id="filter_student_nachname" placeholder="Nachname"
                           value="<?= $_SESSION["filter_student_nachname"] ?>">
                    <button class="rb-button" type="submit" formaction="../booking/controller/kurs_download_xlsx.php">
                        Export als Excel
                    </button>

                </div>
            </form>

            <?php /* ******  SEARCH OPTION FORM END  ****** */ ?>
        </div>
        <?php /*
			<div id="short-link">
				<strong>Shortcuts: </strong>
				<span id="sl-all">Alle Kategorien</span>
				<span id="sl-workshops">Workshops</span>
				<span id="sl-open-trainings">Open Trainings</span>
				<span id="sl-sicherheittrainings">Sicherheitstrainings</span>
				<span id="sl-kid-kurs">Kid Kurs</span>
				<span id="sl-gasttrainer">Gasttrainer</span>
				<span id="sl-privatstunde">Privatstunde</span>
				<span id="sl-schnupperstunde">Schnupperstunde</span>
				
				<span id="sl-level1">Level 1</span>
				<span id="sl-level2">Level 2</span>
				<span id="sl-level3">Level 3</span>
				<span id="sl-level4">Level 4</span>
				<span id="sl-level5">Level 5</span>
				<span id="sl-level6">Level 6</span>
				<span id="sl-level7">Level 7</span>
				<span id="sl-level8">Level 8</span>
			</div> */ ?>
        <div id="table-container">
            <?php /* ******  COURSE TABLE BEGIN  ****** */

            if ($_SESSION["filter_only_todo"] == 1) {


                $db_functions->courses->db_load_table_courses($_SESSION["filter_trainer"],
                    $_SESSION["filter_zeitraum"],
                    $_SESSION["filter_from_date"],
                    $_SESSION["filter_to_date"],
                    $_SESSION["filter_location"],
                    $_SESSION["filter_categories"],
                    $_SESSION["filter_subcategories"],
                    $_SESSION["filter_status"],
                    $_SESSION["filter_publishing"],
                    $_SESSION["filter_course_number"],
                    $_SESSION["filter_student_email"],
                    $_SESSION["filter_list_view_mode"],
                    'all', // Kursformat
                    'all', // Kursart
                    'all', // Kursebene
                    $_SESSION["filter_only_last_modified"],
                    $_SESSION["filter_only_todo"],
                    $_SESSION["filter_student_vorname"],
                    $_SESSION["filter_student_nachname"]);
            } else {
                /* ******  COURSE TABLE END  ****** */ ?>
                <br/>
                Loading ...
            <?php } ?>
        </div>

    </div>
</div>
<div style="display: none;" id="dialog-info1" title="Kurs auswählen">
    <p>Bitte zunächst einen Kurs auswählen.</p>
</div>
</body>
<div style="display: none;" id="dialog-publishing" title="Veröffentlichen?">
    <p>Sollen alle Kurse, deren Statusse aktiv sind und die bereit zur Veröffentlichung sind, jetzt veröffentlicht
        werden?</p>
</div>
<div style="display: none;" id="dialog-publishing-confirmation" title="Veröffentlichung">
    <p>Veröffentlichung erfolgreich durchgeführt!</p>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        function rb_init_datepicker(p_element) {
            p_element.datepicker({
                <?php $rb_configuration->get_datepicker_options(); ?>
            });

        };

        function rb_init_selectable(p_element) {

            $(p_element + ' tr').addClass('rb-selectable');
            $('.rb-selectable:not(:first-child)').on('click mousedown', function () {

                $('.ui-selected').toggleClass('ui-selected');
                $('.ui-selecting').toggleClass('ui-selecting');
                $(this).addClass('ui-selected ui-selecting');

            });

        };

        var alt_key_is_pressed = false;

        $(window).keydown(function (event) {
            alt_key_is_pressed = true;
        });
        $(window).keyup(function (event) {
            alt_key_is_pressed = false;
        });


        function rb_get_selected_id() {
            return $(".ui-selected, ui-selecting").first().attr('course_id');
        };

        function rb_set_loading_effect() {
            $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
        };

        $("form").submit(function () {   // loading effect
            rb_set_loading_effect();
        });

        // ***************************************

        rb_init_datepicker($("#begin_date1, #end_date1"));



        <?php         /* ******  CLICK ACTION BEGIN  ****** */    ?>

        $("#button-registrations").click(function () {
            var course_id = rb_get_selected_id();
            if (!course_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + (course_id) + '&reg=0';
            }
            return false;
        });
        $("#button-attendance").click(function () {
            var course_id = rb_get_selected_id();
            if (!course_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' + (course_id);
            }
            return false;
        });

        $("#button-safety-check").click(function () {
            var course_id = rb_get_selected_id();
            if (!course_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=safety_check&course=' + (course_id);
            }
            return false;
        });
        $("#button-course-notes").click(function () {
            var course_id = rb_get_selected_id();
            if (!course_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + (course_id);
            }
            return false;
        });
        $("#button-simple-mode").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?php echo $rb_path_self?>?view=course_list&view_mode=simple';
            return false;
        });
        $("#button-course-detail").click(function () {
            var course_id = rb_get_selected_id();
            if (!course_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + (course_id);
            }
            return false;
        });

        $("#button-create-course").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?php echo $rb_path_self?>?view=course_new&action=reset_course_values';
            return false;
        });

        function init_click_events() {

            $(".button-registrations").click(function () {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + $(this).attr('course_id') + '&reg=0';
                return false;
            });
            $(".button-course-name").click(function () {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + $(this).attr('course_id');
                return false;
            });

            $(".courses-list-table  tr:not(:first-child)").on('dblclick taphold', function (evt) {

                if (alt_key_is_pressed) return false;

                rb_set_loading_effect();
                <?php if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
                window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + ($(this).attr('course_id'));
                <?php } else { ?>
                window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' + ($(this).attr('course_id'));
                <?php } ?>
                return false;
            });

            <?php if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>

            $('.courses-list-table-disabled  tr:not(:first-child)').mousedown(function (event) {
                if (event.which == 3) {
                    window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + $(this).attr('course_id') + '&reg=0';
                    return false;
                }
            });
            <?php } ?>

        };


        <?php         /* ******  CLICK ACTION END  ****** */    ?>



        function get_dialog_info1() {
            $("#dialog-info1").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 300,
                modal: true,
                buttons: {
                    "Okay": function () {
                        $(this).dialog("close");
                    }
                }
            });
        };

        $(function () {
            $("form input, form select, body").keypress(function (e) {
                if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                    Document.form1.submit();
                    return false;
                }
            });
        });

        $("select, #begin_date1, #end_date1").on('change', function () {
            reload_table(0);
        });

        $("#filter_course_number").on('keyup', function () {
            var course_num = $("#filter_course_number").val();
            if ($.isNumeric(course_num) && course_num > 99) {
                reload_table(200);
            } else {
                reload_table(400);
            }
        });
        $("#filter_student_email").on('keyup', function () {
            reload_table(400);
        });
        $("#filter_student_vorname").on('keyup', function () {
            reload_table(400);
        });
        $("#filter_student_nachname").on('keyup', function () {
            reload_table(400);
        });


        var delayTimer;

        function reload_table(p_delay_in_ms) {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function () {
                var url = "controller/ajax_functions/reload_courses.php";
                url = url + '?t=' + $("#filter_trainer").val();
                url = url + '&l=' + $("#filter_location").val();
                url = url + '&c=' + $("#filter_categories").val();
                url = url + '&sc=' + $("#filter_subcategories").val();
                url = url + '&cf=' + $("#filter_course_format").val();
                url = url + '&ct=' + $("#filter_course_type").val();
                url = url + '&cl=' + $("#filter_course_level").val();
                url = url + '&s=' + $("#filter_status").val();
                url = url + '&p=' + $("#filter_publishing").val();
                url = url + '&z=' + $("#filter_zeitraum").val();
                url = url + '&b=' + $("#begin_date1").val();
                url = url + '&e=' + $("#end_date1").val();
                var course_nr = String($("#filter_course_number").val());
                course_nr = course_nr.replace(/\s/g, '');
                url = url + '&n=' + course_nr;
                url = url + '&se=' + $("#filter_student_email").val();
                url = url + '&sv=' + $("#filter_student_vorname").val();
                url = url + '&sn=' + $("#filter_student_nachname").val();
                url = url + '&lvm=' + $("#filter_list_view_mode").val();
                $("#table-container").load(url, function () {

                    init_click_events();
                    rb_init_selectable('.courses-list-table');
                });

            }, p_delay_in_ms);
        }

        function get_dialog_publishing() {
            $("#dialog-publishing").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 450,
                modal: true,
                buttons: {
                    "Ja, veröffentlichen": function () {
                        $(this).dialog("close");
                        $("#dialog-publishing-confirmation").load("controller/ajax_functions/publish_now.php", function () {
                            $("#dialog-publishing-confirmation").dialog({
                                position: {my: 'top', at: 'top+80'},
                                resizable: true,
                                width: 450,
                                modal: true,
                                buttons: {
                                    "Schließen": function () {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                        });


                    },
                    "Nein": function () {
                        $(this).dialog("close");
                    }
                }
            });
        };

        $("#button-publishing").click(function () {
            get_dialog_publishing();
        });

        function initial_subcut_options() {
            $(".rb-options-subcat-all:not(.rb-options-subcat-" + $('#c-category').val() + ")").hide();
            $(".rb-options-subcat-" + $('#filter_categories').val()).show();
        };
        function change_subcut_options() {
            $(".rb-options-subcat-all:not(.rb-options-subcat-" + $('#c-category').val() + ")").hide();
            $(".rb-options-subcat-all:not(.rb-options-subcat-" + $('#c-category').val() + ")").attr('selected', false);
            $(".rb-options-subcat-all1").attr('selected', true);
            $(".rb-options-subcat-" + $('#filter_categories').val()).show();
        };

        initial_subcut_options();

        $("select[name='filter_categories']").on('change', function () {
            change_subcut_options();
        });


        // #######################################

        function change_course_type_options() {
            $('#filter_course_type option:not(:first-child)').hide();
            $('#filter_course_level option:not(:first-child)').hide();
            var filter_course_format_id = $('#filter_course_format').val();
            if ('all' == filter_course_format_id) {
                $('#filter_course_type option').show();
            } else {
                $('#filter_course_type option[course_format_id="' + filter_course_format_id + '"]').show();
            }
        };
        change_course_type_options();
        $('#filter_course_format').on('change', function () {
            $('#filter_course_type').val('all');
            $('#filter_course_level').val('all');
            change_course_type_options();
        });

        function change_course_level_options() {
            $('#filter_course_level option:not(:first-child)').hide();
            var filter_course_type_id = $('#filter_course_type').val();
            if ('all' == filter_course_type_id) {
                $('#filter_course_level option').show();
            } else {
                $('#filter_course_level option[course_type_id="' + filter_course_type_id + '"]').show();
            }
        };
        change_course_level_options();
        $('#filter_course_type').on('change', function () {
            $('#filter_course_level').val('all');
            change_course_level_options();
        });

        //  ##################################

        $("#sl-all").click(function () {
            $('#filter_categories').val("all");
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-workshops").click(function () {
            $('#filter_categories').val(1);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-open-trainings").click(function () {
            $('#filter_categories').val(3);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-sicherheittrainings").click(function () {
            $('#filter_categories').val(4);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-kid-kurs").click(function () {
            $('#filter_categories').val(7);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-gasttrainer").click(function () {
            $('#filter_categories').val(5);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-privatstunde").click(function () {
            $('#filter_categories').val(8);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-schnupperstunde").click(function () {
            $('#filter_categories').val(9);
            change_subcut_options();
            reload_table(0);
        });
        $("#sl-level1").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(1);
            reload_table(0);
        });
        $("#sl-level2").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(2);
            reload_table(0);
        });
        $("#sl-level3").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(3);
            reload_table(0);
        });
        $("#sl-level4").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(4);
            reload_table(0);
        });
        $("#sl-level5").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(5);
            reload_table(0);
        });
        $("#sl-level6").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(6);
            reload_table(0);
        });
        $("#sl-level7").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(7);
            reload_table(0);
        });
        $("#sl-level8").click(function () {
            $('#filter_categories').val(2);
            change_subcut_options();
            $('#filter_subcategories').val(8);
            reload_table(0);
        });
        $("#button-last-modified").click(function () {

            $('#form1').append("<input type='hidden' name='filter_only_last_modified' value='1' />");
            $('#form1').submit();
        });
        $("#button-open-tasks").click(function () {

            $('#form1').append("<input type='hidden' name='filter_only_todo' value='1' />");
            $('#form1').submit();
        });
        $("#button-reset-options").click(function () {

            $('#form1').append("<input type='hidden' name='action' value='c_reset_course_filter_options' />");
            $('#form1').submit();
        });

        <?php if(!($_SESSION["filter_only_todo"] == 1)) {
        $_SESSION["filter_only_todo"] == 0
        ?>
        reload_table(0);
        <?php } ?>

    });
</script>

</html>