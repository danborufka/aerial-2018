<?php
header('Content-Type: text/html; charset=utf-8');
defined("main-call") or die();
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title><?= $rb_configuration->title_of_web_application_backend ?></title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="Ing. Roman Breitschopf, BA">
    <link href="./lib/jqueryui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="./css/general.css" rel="stylesheet" type="text/css"/>
    <link href="./css/header.css" rel="stylesheet" type="text/css"/>
    <link href="./css/course_registrations_view.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <script src="./lib/jquery.js"></script>
    <script src="./lib/jquery.mobile.min.js"></script>
    <script src="./lib/jqueryui/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#button-go-back").click(function () {
                $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
                window.location.href = '<?=$rb_path_self?>?view=course_list';
            });
            $("#button-refresh").click(function () {
                $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
                window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=';
                window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=<? if (isset($_SESSION["r_course_id"])) echo $_SESSION["r_course_id"]?>';
            });
        });
    </script>
</head>
<body>
<div id="rb-container">
    <?php require $rb_configuration->relative_path_of_header_php;
    if (!(isset($_SESSION["r_edit_id"]))) $_SESSION["r_edit_id"] = 0; ?>
    <div id="container">
        <div id="title">
            <h2>Kurs - Anmeldungen</h2>
        </div>
        <div id="button-bar">
            <div id="button-go-back" class="rb-button3">
                Zurück
            </div>
            <div id="button-refresh" class="rb-button3">
                Neuladen
            </div>
            <div id="button-course-detail" class="rb-button3">
                Kursdetails
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
            <div id="button-registration-link" class="rb-button3">
                Anmelde- Link
            </div>
        </div>
        <?
        if (isset($_SESSION["r_is_expanded"]) && $_SESSION["r_is_expanded"] == "true") {
            unset($_SESSION["r_is_expanded"]);
            $r_expanded = true;
        } else {
            $r_expanded = false;
        } ?>

        <div id="course-box-container" class="clearfix">
            <div id="course-detail-container">
                <?
                $dates = null;
                $dates = $db_functions->courses_extras->db_load_course_detail($_SESSION["r_course_id"]);
                ?>
            </div>
        </div>
        <div id="default-button-bar-container" <?php if ($_SESSION["r_edit_id"] != 0) echo "style='display:none'" ?>>
            <div id="button-edit-registration" class='rb-button3' <?php if ($r_expanded) echo "style='display:none'" ?>>
                Bearbeiten
            </div>
            <div id="button-send-email" class='rb-button3' <?php if ($r_expanded) echo "style='display:none'" ?>>
                System- Email senden
            </div>
            <div id="button-expand-registration"
                 class='rb-button3' <?php if ($r_expanded) echo "style='display:none'" ?>>
                Teilnehmer hinzufügen ►
            </div>
            <div id="button-aggregated-emails" class='rb-button3' <?php if ($r_expanded) echo "style='display:none'" ?>>
                E-Mail-Adressen
            </div>
            <div id="manual-registration-container-expanded"
                 class="clearfix" <?php if ($r_expanded) echo "style='display:inline-block'" ?>">
            <div class="manual-registration-element">
                <div id="button-minimize-registration" class="rb-button">
                    ◄
                </div>
            </div>
            <form id="manual-registration-form" method="POST" action="process.php" class="input-container clear-fix">
                <fieldset>
                    <legend>Teilnehmer manuell anmelden</legend>
                    <div class="input-row">
                        <label for="r_email">E-Mail- Adresse*:</label>
                        <?php if (!isset($_SESSION["r_email"])) $_SESSION["r_email"] = "" ?>
                        <input name="r_email" type="text" id="r_email" value="<?php echo $_SESSION["r_email"] ?>">
                        <input name="r_course_id" type="hidden" value="<?php echo $_SESSION["r_course_id"] ?>">
                        <input name="r_is_expanded" type="hidden" value="true">
                        <input name='focus' type='hidden' value='registration-form'>
                        <button form="manual-registration-form" type="submit" name="action"
                                value="r_manual_registration" class="rb-button">Teilnehmer manuell anmelden
                        </button>
                    </div>
                </fieldset>
            </form>
            <div class="manual-registration-element">
                <div id="button-student-list" class="rb-button3">zur Teilnehmersuche</div>
            </div>
        </div>
    </div>
    <div id="edit-mode-button-bar-container" <?php if ($_SESSION["r_edit_id"] == 0) echo "style='display:none'" ?>>
        <button form="registration-form" name="action" value="r_update_registration" class='id-button-save rb-button'>
            Speichern
        </button>
        <div id="button-edit-cancel" class='rb-button'>
            Änderung verwerfen
        </div>
    </div>
    <form id="registration-form" method="POST" action="process.php">
        <input type="hidden" name="view" value="course_registrations">
        <input name='focus' type='hidden' value='registration-form'>
        <input type="hidden" name="registration_id" value="<?= $_SESSION["r_edit_id"] ?>">
        <input type="hidden" name="r_course_id" value="<?= $_SESSION["r_course_id"] ?>">
        <input type="hidden" name="r_course" value="<?= $_SESSION["r_course_id"] ?>">
        <?
        $db_functions->registrations->db_load_table_registrations($_SESSION["r_course_id"], $_SESSION["r_edit_id"]); ?>
    </form>

</div>
</div>
</body>
<div style="display: none;" id="dialog-info1" title="Kurs auswählen">
    <p>Bitte zunächst eine Anmeldung auswählen.</p>
</div>
<div style="display: none;" id="dialog-save-info" title="Änderung speichern?">
    <p>Sollen Änderungen gespeichert werden?</p>
</div>
<div id="dialog-system-mail" title="System- Email versenden" style="display:none">
    Bitte Art der System- Email auswählen:
</div>
<div id="dialog-set-payed" title="Kursbestätigung senden?" style="display:none">
    Soll jetzt eine Kursbestätigungs- Email versendet werden?
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var course_id = <?php echo $_SESSION["r_course_id"]; ?>;

        function rb_init_selectable(p_element) {

            $(p_element + ' tr').addClass('rb-selectable');
            $('.rb-selectable:not(:first-child)').on('click mousedown', function () {

                $('.ui-selected').toggleClass('ui-selected');
                $('.ui-selecting').toggleClass('ui-selecting');
                $(this).addClass('ui-selected ui-selecting');

            });

        };

        function rb_init_datepicker(p_element) {
            p_element.datepicker({
                <?php $rb_configuration->get_datepicker_options(); ?>
            });

        };

        var alt_key_is_pressed = false;

        $(window).keydown(function (event) {
            alt_key_is_pressed = true;
        });
        $(window).keyup(function (event) {
            alt_key_is_pressed = false;
        });


        <? if (!(isset($_SESSION["r_edit_id"]) && $_SESSION["r_edit_id"] != '0')) echo "rb_init_selectable('.table-registrations');"; ?>

        rb_init_datepicker($("#r_payment_reminder, #r_dunning"));

        function rb_set_loading_effect() {
            $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
        };

        $("form").submit(function () {   // loading effect
            rb_set_loading_effect();
        });


        // ***************************************


        $(".table-registrations  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
            if (alt_key_is_pressed) return false;
            <? if($_SESSION["r_edit_id"] == '0') { ?>
            rb_set_loading_effect();
            window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=' + $(this).attr('registration_id') + '&focus=registration-form';
            <? } else { ?>
            get_dialog_save($(this).attr('registration_id'));
            <? } ?>
        });


        $("#button-course-detail").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?=$rb_path_self?>?view=course_detail&course=' + course_id;
        });

        $("#button-attendance").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?=$rb_path_self?>?view=attendance&course=' + course_id;
        });

        $("#button-safety-check").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?=$rb_path_self?>?view=safety_check&course=' + course_id;
        });
        $("#button-course-notes").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?=$rb_path_self?>?view=course_notes&course=' + course_id;
        });

        $("#button-edit-cancel").click(function () {
            rb_set_loading_effect();
            window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=0' + '&focus=registration-form';
        });


        $("#button-expand-registration").click(function () {
            $("#button-expand-registration, #button-edit-registration, #button-send-conf-email, #button-send-email, #button-aggregated-emails").hide();
            $("#manual-registration-container-expanded").show();
        });

        $("#button-minimize-registration").click(function () {
            $("#manual-registration-container-expanded").hide();
            $("#button-expand-registration, #button-edit-registration, #button-send-conf-email, #button-send-email, #button-aggregated-emails").show();
        });

        $("#button-student-list").click(function () {

            window.open('<?=$rb_path_self?>?view=student_list&w1=small&c1=<?=$_SESSION["r_course_id"]?>', 'NewWin',
                'menubar=no, resizeable=no,toolbar=no,status=no,width=1150,height=665');
        });

        $("#button-edit-registration").click(function () {
            var registration_id = $(".ui-selected, ui-selecting").first().attr('registration_id');
            if (!registration_id) {
                get_dialog_info1();
            } else {
                rb_set_loading_effect();
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=' + registration_id + '&focus=registration-form';
            }
        });


        $("#button-registration-link").click(function () {
            <? if (!(isset($_SESSION['r_registration_code']))) $_SESSION['r_registration_code'] = 'undefined'; ?>
            window.open('<?=str_replace("booking/", "", $rb_path_self)?>anmeldung/?kurs=<?=$_SESSION['r_course_id']?>&code=<?=$_SESSION['r_registration_code']?>', '_blank');
        });

        $(".button-set-payed").click(function () {
            open_dialog_set_payed($(this).attr('pay_id'));
        });

        $("#button-send-email").click(function () {
            var registration_id = $(".ui-selected, ui-selecting").first().attr('registration_id');
            if (!registration_id) {
                get_dialog_info1();
            } else {
                $("#dialog-system-mail").html("Bitte Art der System- Email auswählen:");
                open_dialog_for_system_mails(registration_id);
            }
        });


        <?php if(isset($r_email_validation_for_registration["negative_result"])) { ?>

        alert('<?php echo $r_email_validation_for_registration["negative_result"] ?>');

        <?php
        $r_email_validation_for_registration = false;
        }?>

        $("#button-aggregated-emails").click(function () {
            get_dialog_aggregated_emails();
        });

        function get_dialog_aggregated_emails() {
            $('#dialog-aggregated-emails').dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 400,
                modal: true,
                buttons: {
                    "Schließen": function () {
                        $(this).dialog("close");
                    }
                }
            })
        }

        function get_dialog_info1() {
            $("#dialog-info1").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 450,
                modal: true,
                buttons: {
                    "Okay": function () {
                        $(this).dialog("close");
                    }
                }
            });
        }

        function get_dialog_save(p_registration_id) {
            $("#dialog-save-info").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 450,
                modal: true,
                buttons: {
                    "Speichern": function () {
                        $(this).dialog("close");
                        $('#registration-form').append("<input type='hidden' name='r_next_edit_id' value='" + p_registration_id + "'/>");
                        $('#registration-form').append("<input type='hidden' name='action' value='r_update_and_edit_next' />");
                        $('#registration-form').submit();

                    },
                    "Verwerfen": function () {
                        $(this).dialog("close");
                        if (p_registration_id != <?=$_SESSION["r_edit_id"]?>) {
                            window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=' + p_registration_id + '&focus=registration-form';
                        } else {
                            window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=0' + '&focus=registration-form';
                        }
                    }
                }
            });
        };


        function open_dialog_set_payed(p_reg_id) {
            $("#dialog-set-payed").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 470,
                modal: true,
                buttons: {
                    "Ja, versenden": function () {
                        $(this).dialog("close");
                        send_system_mail_set_payed(p_reg_id, "standard_confirmation");
                    },
                    "Nein, keine E-Mail senden": function () {
                        $(this).dialog("close");
                        window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&action=r_set_to_paid&pay_id=' + p_reg_id + '&focus=registration-form';
                    },
                    "Abbrechen": function () {
                        $(this).dialog("close");
                    }
                }
            });
        }

        function open_dialog_for_system_mails(p_reg_id) {
            $("#dialog-system-mail").dialog({
                position: {my: 'top', at: 'top+80'},
                resizable: true,
                width: 450,
                modal: true,
                buttons: {
                    "vorgemerkt: Verifizierung": function () {
                        $(this).dialog("close");
                        send_system_mail(p_reg_id, "verification");
                    },
                    "angemeldet: Zahlungsaufforderung": function () {
                        $(this).dialog("close");
                        send_system_mail(p_reg_id, "regular_payment");
                    },
                    "bezahlt: Bestätigung": function () {
                        $(this).dialog("close");
                        send_system_mail(p_reg_id, "standard_confirmation");
                    },
                    "Warteliste: Platz frei": function () {
                        $(this).dialog("close");
                        send_system_mail_set_waitlist(p_reg_id, "wait_list_place_available");
                    },
                    "Zahlungserinnerung": function () {
                        $(this).dialog("close");
                        send_system_mail_set_payment_reminder(p_reg_id, "payment_reminder");
                    },
                    "Mahnung": function () {
                        $(this).dialog("close");
                        send_system_mail_set_dunning(p_reg_id, "dunning_letter");
                    },
                    "Anmeldebestätigung": function () {
                        $(this).dialog("close");
                        send_system_mail(p_reg_id, "standard_confirmation");
                    },
                    "Abbrechen": function () {
                        $(this).dialog("close");
                    }
                }
            });
        }

        function send_system_mail(p_reg_id, p_type_of_mail) {
            var url = createUrl(p_reg_id, p_type_of_mail);
            open_okay_dialog(url);
        }

        function send_system_mail_set_payed(p_reg_id, p_type_of_mail) {
            var url = createUrl(p_reg_id, p_type_of_mail);
            $("#dialog-system-mail").load(url, function () {
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&action=r_set_to_paid&pay_id=' + p_reg_id + '&focus=registration-form';
            });
        }

        function send_system_mail_set_payment_reminder(p_reg_id, p_type_of_mail) {
            var url = createUrl(p_reg_id, p_type_of_mail);
            $("#dialog-system-mail").load(url, function () {
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&action=r_set_payment_reminder&pay_id=' + p_reg_id + '&focus=registration-form';
            });
        }

        function send_system_mail_set_dunning(p_reg_id, p_type_of_mail) {
            var url = createUrl(p_reg_id, p_type_of_mail);
            $("#dialog-system-mail").load(url, function () {
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&action=r_set_dunning&pay_id=' + p_reg_id + '&focus=registration-form';
            });
        }

        function send_system_mail_set_waitlist(p_reg_id, p_type_of_mail) {
            var url = createUrl(p_reg_id, p_type_of_mail);
            $("#dialog-system-mail").load(url, function () {
                window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + course_id + '&action=r_set_waitlist&pay_id=' + p_reg_id + '&focus=registration-form';
            });
        }

        function open_okay_dialog(url) {
            $("#dialog-system-mail").load(url, function () {
//                this.system_mail_dialog();
                $("#dialog-system-mail").dialog({
                    position: {my: 'top', at: 'top+80'},
                    resizable: true,
                    width: 450,
                    modal: true,
                    buttons: {
                        "Okay": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            });
        }

        function createUrl(p_reg_id, p_type_of_mail) {
            var url = "controller/ajax_functions/send_system_mail_now.php";
            url = url + '?registration_id=' + p_reg_id;
            url = url + '&type=' + p_type_of_mail;
            return url;
        }


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

        $(".email-text").click(function () {
            enable_email_selection(this);
        });


    });

</script>

</html>