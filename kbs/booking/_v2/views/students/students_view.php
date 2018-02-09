<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
    <title>Aerial Silk Booking</title>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="Ing. Roman Breitschopf, BA">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="./lib/jqueryui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="./css/general.css" rel="stylesheet" type="text/css"/>
    <link href="./css/header.css" rel="stylesheet" type="text/css"/>
    <link href="./css/students_view.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <script src="./lib/jquery.js"></script>
    <script src="./lib/jquery.mobile.min.js"></script>
    <script src="./lib/jqueryui/jquery-ui.js"></script>
    <script src="./_v2/js/clientManager.js"></script>
</head>
<body>
<div id="rb-container">
    <?php require $rb_configuration->relative_path_of_header_php;
    $view_name_begin = 'students';
    $pre = 'st';  // = entity pre abbreviation
    ?>
    <div id="container">
        <div class="container-overview">
            <div class="title">
                <h2>Teilnehmer</h2>
            </div>


            <div id="button-bar">
                <div id="button-go-back" onclick="window.location.href ='<?= $rb_path_self ?>?view=main_menu'"
                     class="rb-button">Zurück
                </div>
                <div id="button-detail" class="rb-button">Details</div>
                <div id="button-new" onclick="cm.Students.new();" class="rb-button">Neu</div>

            </div>

            <div id="search-option-container">
                <form class="filter-form" method="post" action="../booking/controller/teilnehmer_download_xlsx.php">
                    <div id="search-option-row1">
                        <label for='filter_surname'>Vorname:</label>
                        <input type="text" id="filter_prename" name="filter_prename"/>
                        <label for="filter_status">Status:</label>
                        <select name='filter_status' id='filter_status' class='select-with-symbols'>
                            <option value='1'>✔ aktiv</option>
                            <option value='2'>✖ deaktiviert</option>
                            <option value='3'>➥ fusioniert</option>
                            <option value='4'>✖✖ gesperrt</option>
                            <option value='5'>@ unverifizierte Email</option>
                            <option value='-2'>Ⓐ alle</option>
                        </select>
                        <label for="filter_membership">Mitgliedschaft:</label>
                        <select name='filter_membership' id='filter_membership' class='select-with-symbols'>
                            <option value='-2'>Ⓐ alle</option>
                            <option value='1'>✔ ja</option>
                            <option value='0'>✖ nein</option>
                            <option value='-3'>! beantragt</option>
                        </select>
                        <label for="filter_newsletter">Newsletter:</label>
                        <select name='filter_newsletter' id='filter_newsletter' class='select-with-symbols'>
                            <option value='-2'>Ⓐ alle</option>
                            <option value='1'>✔ abonniert</option>
                            <option value='0'>✖ nicht abonniert</option>
                        </select>
                        <input type="submit" value="Export als Excel" class="rb-button">
                    </div>
                    <div id="search-option-row2">
                        <label for='filter_prename'>Nachname:</label>
                        <input type="text" id="filter_surname" name="filter_surname"/>
                        <label for='filter_email'>Email:</label>
                        <input type="text" id="filter_email" name="filter_email"/>
                        <label for='filter_mb_paid_date'>bezahlt bis:</label>
                        <select name='filter_mb_paid_date' id='filter_mb_paid_date' class="select-with-symbols">
                            <option value=''>Ⓐ alle</option>
                            <option value='31.12.2016'>31.12.2016</option>
                            <option value='31.12.2017'>31.12.2017</option>
                            <option value='31.12.2018'>31.12.2018</option>
                            <option value='31.12.2019'>31.12.2019</option>
                            <option value='31.12.2020'>31.12.2020</option>
                            <option value='31.12.2021'>31.12.2021</option>
                            <option value='31.12.2022'>31.12.2022</option>
                            <option value='31.12.2023'>31.12.2023</option>
                            <option value='31.12.2024'>31.12.2024</option>
                            <option value='31.12.2025'>31.12.2025</option>
                            <option value='31.12.2026'>31.12.2026</option>
                        </select>
                        <input type="button" onclick="cm.Students.getSearchResult(0);" id="search-button"
                               class="rb-button" value="Suchoptionen anwenden">
                    </div>
                </form>
            </div>

            <div class='rb-table'>
                Loading ...
            </div>

        </div>

        <div class="container-voucher-add" style="display:none">
            <?
            require_once(__DIR__ . '/voucher_detail.php');
            ?>
        </div>
        <div class="container-detail" style="display:none">
            <?
            require_once(__DIR__ . '/detail_form.php');
            ?>
        </div>
        <div class="container-courses" style="display:none">
            <?
            require_once(__DIR__ . '/course_list.php');
            ?>
        </div>
        <div class="container-voucher" style="display:none">
            <?
            require_once(__DIR__ . '/voucher_list.php');
            ?>
        </div>


    </div>
</div>
</body>

<script type="text/javascript">
    $(function () {

        cm.Students.getSearchResult(0);
        // cm.Students.getStudentsFormatSelectOptions();

        $("#button-detail").click(function () {
            var $id = cm.SelectableManager.getSelectedId;
            if (!$id) {
                alert("Bitte Auswahl treffen.");
            } else {
                cm.Students.editWithId($id);
            }
        });


        $(".filter-form select").on('change', function () {
            cm.Students.getSearchResult(0);
        });
        $(".filter-form input[type='text']").on('keyup', function () {
            cm.Students.getSearchResult(400);
        });

        $("form input, form select").keypress(function (e) {
            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                cm.Students.getSearchResult(0);
                return false;
            } else {
                return true;
            }
        });
    });
</script>

</html>