var cmProperties = {
    //axaxAbsoluteUrl_Base: "https://www.aerialsilk.at/de/",
    ajaxServicesUrl_relativePath: "kbs_frontend/course-ajax-services.php",
    ajaxServicesUrl_v1_relativePath: "kbs/anmeldung/ajax-services.php",
    ajaxServicesUrl: null,
    ajaxServicesUrl_v1: null,
    toggleDuration: 250,
    isNewCustomer: true,
    processingSubmitStatus: false
};

var cm = {
    General: {
        initUrl: function () {
            cmProperties.ajaxServicesUrl = cm.General.getBasicURL() + cmProperties.ajaxServicesUrl_relativePath;
            cmProperties.ajaxServicesUrl_v1 = cm.General.getBasicURL() + cmProperties.ajaxServicesUrl_v1_relativePath;
        },
        getBasicURL: function () {
            var url = document.URL;
            url = url.substring(0, url.lastIndexOf("/"));
            url = url.substring(0, url.lastIndexOf("/") + 1);
            url = url.substring(0, url.lastIndexOf("/"));
            url = url.substring(0, url.lastIndexOf("/") + 1);
            return url;
        },
        init: function () {
            cm.General.initUrl();
            cm.Courses.initSlideToggle();
            cm.Courses.getSearchResult();
            jQuery('#booking-form-container').on('submit', 'form', function () {
                cm.CourseRegistration.startSubmit();
                return false;
            });
            jQuery('#voucher-form-container').on('submit', 'form', function () {
                cm.Voucher.startSubmit();
                return false;
            });
            jQuery('#booking-form-container').on('change', '#kbs-new-customer', function () {
                if (jQuery('#kbs-new-customer').is(":checked")) {
                    cm.BookingForm.fillNewCustomerPart();
                    cmProperties.isNewCustomer = true;
                } else {
                    cm.BookingForm.removeNewCustomerPart();
                    cmProperties.isNewCustomer = false;
                }
            });
            cm.HashManager.checkConfirmationHash();
        },
        getAjaxUrl: function (cmd) {
            var time = new Date();
            time = time.getTime();
            var url = cmProperties.ajaxServicesUrl + "?cmd=" + cmd + "&t=" + time;
            return url;
        },
        getAjaxUrl_v1: function (cmd) {
            var time = new Date();
            time = time.getTime();
            var url = cmProperties.ajaxServicesUrl_v1 + "?cmd=" + cmd + "&t=" + time;
            return url;
        },
        getGermanWeekday: function (weekday_number) {
            var weekday_names = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

            return weekday_names[weekday_number];
        },
        delayedResettingOfProcessingSubmitStatus: function () {
            setTimeout(function () {
                cmProperties.processingSubmitStatus = false;
            }, 3000);
        }
    },
    HashManager: {
        init: function () {
            jQuery(window).hashchange(function () {
                alert('hash changed');
            });
        },
        checkConfirmationHash: function () {
            var hash = location.hash;
            if (hash == '#confirmation') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung erfolgreich</h3>');
                h.push('	<p>Die Bestätigung war erfolgreich, eine Bestätigungs-Email wurde versand.</p>');
                h.push('	<p>Bitte überweise den Kursbetrag wie in der Email beschrieben.</p>');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();
            } else if (hash == '#confirmation-waitlist') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung für die Warteliste erfolgreich</h3>');
                h.push('	<p>Die Bestätigung für die Warteliste war erfolgreich, eine Bestätigungs-Email wurde versand.</p>');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();
            } else if (hash == '#confirmation-failed') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung fehlgeschlagen</h3>');
                h.push('	<p>Die Bestätigung ist fehlgeschlagen, womöglich ist die Frist bereits abgelaufen.</p>');
                h.push('	<p>Bitte melde dich erneut an.</p>');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();
            } else if (hash == '#confirmation-ca') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung erfolgreich</h3>');
                h.push('	<p>Die Bestätigung war erfolgreich, eine Bestätigungs-Email wurde versand.</p>');
                h.push('	<p>Bitte den Kursbetrag am Kurstag vor Ort bezahlen.</p>');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();
            } else if (hash == '#confirmation-already-done') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung wurde bereits durchgeführt</h3>');
                h.push('	<p>Die Bestätigung wurde zu einem früheren Zeitpunkt bereits durchgeführt.');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();

            } else if (hash == '#confirmation-invalid') {
                var divContainer = jQuery('#booking-form-container');
                divContainer.empty();
                var h = [];
                h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Schließen</a></div>');
                h.push('<div id="booking-form-box">');
                h.push('	<h3>Bestätigung fehlgeschlagen oder Frist abgelaufen.</h3>');
                h.push('	<p>Bitte melde dich erneut zum Kurs an.');
                h.push('</div>');
                divContainer.html(h.join(''));
                divContainer.show();
            }
        }
    },
    Courses: {
        getSearchResult: function () {
            var cmd = 'Courses.GetSearchResult';
            jQuery.post(cm.General.getAjaxUrl(cmd), {}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {

                    cm.Courses.createCourseGrid(d.data);
                    // var divMobileContainer = jQuery("#course-mobile-container");
                    // divMobileContainer.empty();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        createCourseGrid: function (da) {
            var divContainer = jQuery("#course-container");
            divContainer.empty();

            var length = da.length;
            if (length == 0) {
                divContainer.append('<h3>Keine Kurse gefunden</h3>');
                return;
            }

            for (var i = 0; i < length; i++) {
                var currFormat = da[i];
                var h = [];
                h.push('<div class="course-format-box course-format-box-' + currFormat.course_format_id + '" course_format_id="' + currFormat.course_format_id + '">');
                h.push('	<h3>' + currFormat.course_format_name + '</h3>');

                h.push('</div');
                divContainer.append(h.join(''));
                courseLevels = currFormat.course_levels;
                lengthCourseLevels = courseLevels.length;
                var previousCourseTypeName = '';
                for (var k = 0; k < lengthCourseLevels; k++) {
                    var h = [];
                    var currLevel = courseLevels[k];

                    if (previousCourseTypeName != currLevel.course_type_name) {
                        h.push('	<h4>' + currLevel.course_type_name + '</h4>');
                    }
                    previousCourseTypeName = currLevel.course_type_name;

                    var price = currLevel.price.replace('.00', '');
                    var member_price = currLevel.member_price.replace('.00', '');
                    var price_text = price + '€ | ' + member_price + '€';
                    var unit_text;
                    var level_id = currLevel.course_level_id;
                    if (currLevel.units == 1) {
                        unit_text = '1 Einheit';
                    } else {
                        unit_text = currLevel.units + ' Einheiten';
                    }
                    if (currLevel.description == null) currLevel.description = '';
                    h.push('<div class="course-level-box" course_level_id="' + level_id + '">');
                    h.push('	<div class="course-level-head">');
                    h.push('		<i class="fa fa-caret-right"></i> &nbsp;&nbsp;');
                    h.push(currLevel.course_level_name + '&nbsp;&nbsp;&nbsp;&nbsp;' + unit_text + '&nbsp;&nbsp;&nbsp;&nbsp;' + price_text);
                    h.push('	</div>');
                    h.push('	<div class="course-level-content">');
                    h.push('		<p>' + currLevel.description + '</p>');

                    h.push('		<div class="desktop-table-level" id="desktop-table-level-' + level_id + '">');
                    h.push('		</div>');
                    h.push('		<div class="small-grid-level" id="small-grid-level-' + level_id + '">');
                    h.push('		</div>');

                    h.push('	</div>');
                    h.push('</div>');
                    jQuery('.course-format-box-' + currFormat.course_format_id).append(h.join(''));

                    jQuery('#desktop-table-level-' + level_id).html(cm.Courses.createDesktopTableForLevels(currLevel));
                    jQuery('#small-grid-level-' + level_id).html(cm.Courses.createMobileGridForLevels(currLevel));
                }
            }
            cm.Courses.createVouchers();
        },
        createVouchers: function () {
            var divContainer = jQuery("#course-container");
            var h = [];
            h.push('<div class="course-format-box" style="display: none;">');
            h.push('	<h3>Open Silk Blöcke</h3>');
            h.push('<div class="course-level-box">');
            h.push('	<div class="course-level-head">');
            h.push('		<i class="fa fa-caret-right"></i> &nbsp;&nbsp;');
            h.push('        Open Silk Blöcke');
            h.push('	</div>');
            h.push('	<div class="course-level-content">');
            h.push('		<p>Um einen Open Silk Kurs zu besuchen musst du zuerst ein Sicherheitstraining absolvieren.</p>');
            //DesktopView
            h.push('		<div class="desktop-table-level" >');
            h.push('		<table>');
            h.push('			<tbody>');
            h.push('				<tr>');
            h.push('					<th>Anzahl</th>');
            h.push('					<th>Preis</th>');
            h.push('					<th></th>');
            h.push('				</tr>');
            h.push('				<tr>');
            h.push('					<td>10 Open Silk</td>');
            h.push('					<td>60 €</td>');
            h.push('					<td class="booking-button-wrapper"><a onclick="cm.Voucher.openVoucherForm(10, 60)" href="#book-voucher" class="booking-button">ANFORDERN</a></td>');
            h.push('				</tr>');
            h.push('				<tr>');
            h.push('					<td>20 Open Silk</td>');
            h.push('					<td>100 €</td>');
            h.push('					<td class="booking-button-wrapper"><a onclick="cm.Voucher.openVoucherForm(20, 100)" href="#book-voucher" class="booking-button">ANFORDERN</a></td>');
            h.push('				</tr>');
            h.push('			</tbody>');
            h.push('		</table>');
            h.push('		</div>');
            //Mobile View
            h.push('		<div class="small-grid-level">');
            h.push('            <div class="mobile-course-box kbs-clearfix">');
            h.push('	               <ul>');
            h.push('		            <li>Anzahl: 10 Open Silk</li>');
            h.push('		            <li>Preis: 60 €</li>');
            h.push('	               </ul>');
            h.push('	            <div class="booking-button-wrapper kbs-clearfix"><a onclick="cm.Voucher.openVoucherForm(10, 60)" href="#book-voucher" class="booking-button">ANFORDERN</a></div>');
            h.push('            </div>');
            h.push('            <div class="mobile-course-box kbs-clearfix">');
            h.push('	               <ul>');
            h.push('		            <li>Anzahl: 20 Open Silk</li>');
            h.push('		            <li>Preis: 100 €</li>');
            h.push('	            </ul>');
            h.push('	            <div class="booking-button-wrapper kbs-clearfix"><a onclick="cm.Voucher.openVoucherForm(20, 100)" href="#book-voucher" class="booking-button">ANFORDERN</a></div>');
            h.push('            </div>');
            h.push('		</div>');

            h.push('	</div>');
            h.push('</div>');
            h.push('</div>');
            divContainer.append(h.join(''));
        },
        createDesktopTableForLevels: function (currLevel) {
            var h = [];
            h.push('		<table>');
            h.push('			<tbody>');
            h.push('				<tr>');
            h.push('					<th>Wochentag</th>');
            h.push('					<th>Zeitraum</th>');
            h.push('					<th>Uhrzeit</th>');
            h.push('					<th class="small-hidden">Studio</th>');
            h.push('					<th class="small-hidden">Trainerin</th>');
            h.push('					<th class="small-hidden">Kurs-Nr.</th>');
            h.push('					<th></th>');
            h.push('				</tr>');
            courses = currLevel.courses;
            lengthCourses = courses.length;
            for (var j = 0; j < lengthCourses; j++) {
                var currCourse = courses[j];
                var anmeldeFunction = 'cm.BookingForm.openBookingForm(\'' + currCourse.course_id + '\',\'' + currCourse.registration_code + '\')';
                var anmeldeText;
                var trainer = '';
                var variableDates = false;

                if (currCourse.trainer2_name != null) {
                    trainer = currCourse.trainer1_name + ' & ' + currCourse.trainer2_name;
                } else {
                    trainer = currCourse.trainer1_name;
                }
                if (currCourse.free_places_count > 0) {
                    var platzText;
                    if (currCourse.free_places_count == 1) {
                        platzText = ' Platz';
                    } else {
                        platzText = ' Plätze';
                    }
                    anmeldeText = 'BUCHEN (' + currCourse.free_places_count + platzText + ' frei)';
                } else {
                    anmeldeText = 'BUCHEN (Warteliste)';
                }
                var not_on;
                if (currCourse.not_on == null || currCourse.not_on == '') {
                    not_on = '';
                } else {
                    not_on = '<br/>(entfällt am ' + currCourse.not_on + ')';
                }
                if (currCourse.date1 == currCourse.end) {
                    var course_date = currCourse.date1;
                } else {
                    var course_date = currCourse.date1 + ' - ' + currCourse.end;

                    if ((currCourse.time_begin2 != null && currCourse.time_begin2 != currCourse.time_begin) ||
                        (currCourse.time_begin3 != null && currCourse.time_begin3 != currCourse.time_begin) ||
                        (currCourse.time_begin4 != null && currCourse.time_begin4 != currCourse.time_begin) ||
                        (currCourse.time_begin5 != null && currCourse.time_begin5 != currCourse.time_begin) ||
                        (currCourse.time_begin6 != null && currCourse.time_begin6 != currCourse.time_begin) ||
                        (currCourse.time_begin7 != null && currCourse.time_begin7 != currCourse.time_begin) ||
                        (currCourse.time_begin8 != null && currCourse.time_begin8 != currCourse.time_begin) ||
                        (currCourse.time_begin9 != null && currCourse.time_begin9 != currCourse.time_begin) ||
                        (currCourse.time_begin10 != null && currCourse.time_begin10 != currCourse.time_begin) ||
                        (currCourse.time_begin11 != null && currCourse.time_begin11 != currCourse.time_begin) ||
                        (currCourse.time_begin12 != null && currCourse.time_begin12 != currCourse.time_begin) ||
                        (currCourse.time_end2 != null && currCourse.time_end2 != currCourse.time_end) ||
                        (currCourse.time_end3 != null && currCourse.time_end3 != currCourse.time_end) ||
                        (currCourse.time_end4 != null && currCourse.time_end4 != currCourse.time_end) ||
                        (currCourse.time_end5 != null && currCourse.time_end5 != currCourse.time_end) ||
                        (currCourse.time_end6 != null && currCourse.time_end6 != currCourse.time_end) ||
                        (currCourse.time_end7 != null && currCourse.time_end7 != currCourse.time_end) ||
                        (currCourse.time_end8 != null && currCourse.time_end8 != currCourse.time_end) ||
                        (currCourse.time_end9 != null && currCourse.time_end9 != currCourse.time_end) ||
                        (currCourse.time_end10 != null && currCourse.time_end10 != currCourse.time_end) ||
                        (currCourse.time_end11 != null && currCourse.time_end11 != currCourse.time_end) ||
                        (currCourse.time_end12 != null && currCourse.time_end12 != currCourse.time_end)) {
                        variableDates = true;
                    }
                }
                var anmeldeLink = '<a onclick="' + anmeldeFunction + '" href="#book-course" class="booking-button">' + anmeldeText + '</a>';
                h.push('<tr>');
                h.push('	<td>' + cm.General.getGermanWeekday(currCourse.weekday) + '</td>');
                h.push('	<td>' + course_date + not_on + '</td>');
                // h.push('	<td>' + currCourse.time_begin + ' - ' + currCourse.time_end + variableDates?'<br/>Uhrzeit variabel': '' +'</td>');
                if (!variableDates) {
                    h.push('	<td>' + currCourse.time_begin + ' - ' + currCourse.time_end + '</td>');
                } else {
                    h.push('	<td>' + currCourse.time_begin + ' - ' + currCourse.time_end + '</br>Uhrzeit Variabel</td>');
                }
                h.push('	<td class="small-hidden">' + currCourse.location_name + '</td>');
                h.push('	<td class="small-hidden">' + trainer + '</td>');
                h.push('	<td class="small-hidden">' + currCourse.course_id + '</td>');
                h.push('	<td class="booking-button-wrapper">' + anmeldeLink + '</td>');
                h.push('</tr>');
            }
            h.push('			</tbody>');
            h.push('		</table>');
            return h.join('');
        },
        createMobileGridForLevels: function (currLevel) {
            var h = [];
            courses = currLevel.courses;
            lengthCourses = courses.length;
            for (var j = 0; j < lengthCourses; j++) {
                var currCourse = courses[j];
                var anmeldeFunction = 'cm.BookingForm.openBookingForm(\'' + currCourse.course_id + '\',\'' + currCourse.registration_code + '\')';
                var anmeldeText;
                var trainer = '';
                var variableDates = false;
                if (currCourse.trainer2_name != null) {
                    trainer = currCourse.trainer1_name + ' & ' + currCourse.trainer2_name;
                } else {
                    trainer = currCourse.trainer1_name;
                }
                if (currCourse.free_places_count > 0) {
                    var platzText;
                    if (currCourse.free_places_count == 1) {
                        platzText = ' Platz';
                    } else {
                        platzText = ' Plätze';
                    }
                    anmeldeText = 'BUCHEN (' + currCourse.free_places_count + platzText + ' frei)';
                } else {
                    anmeldeText = 'BUCHEN (Warteliste)';
                }
                var not_on;
                if (currCourse.not_on == null || currCourse.not_on == '') {
                    not_on = '';
                } else {
                    not_on = '<br/>(entfällt am ' + currCourse.not_on + ')';
                }
                if (currCourse.date1 == currCourse.end) {
                    var course_date = currCourse.date1;
                } else {
                    var course_date = currCourse.date1 + ' - ' + currCourse.end;
                }
                if ((currCourse.time_begin2 != null && currCourse.time_begin2 != currCourse.time_begin) ||
                    (currCourse.time_begin3 != null && currCourse.time_begin3 != currCourse.time_begin) ||
                    (currCourse.time_begin4 != null && currCourse.time_begin4 != currCourse.time_begin) ||
                    (currCourse.time_begin5 != null && currCourse.time_begin5 != currCourse.time_begin) ||
                    (currCourse.time_begin6 != null && currCourse.time_begin6 != currCourse.time_begin) ||
                    (currCourse.time_begin7 != null && currCourse.time_begin7 != currCourse.time_begin) ||
                    (currCourse.time_begin8 != null && currCourse.time_begin8 != currCourse.time_begin) ||
                    (currCourse.time_begin9 != null && currCourse.time_begin9 != currCourse.time_begin) ||
                    (currCourse.time_begin10 != null && currCourse.time_begin10 != currCourse.time_begin) ||
                    (currCourse.time_begin11 != null && currCourse.time_begin11 != currCourse.time_begin) ||
                    (currCourse.time_begin12 != null && currCourse.time_begin12 != currCourse.time_begin) ||
                    (currCourse.time_end2 != null && currCourse.time_end2 != currCourse.time_end) ||
                    (currCourse.time_end3 != null && currCourse.time_end3 != currCourse.time_end) ||
                    (currCourse.time_end4 != null && currCourse.time_end4 != currCourse.time_end) ||
                    (currCourse.time_end5 != null && currCourse.time_end5 != currCourse.time_end) ||
                    (currCourse.time_end6 != null && currCourse.time_end6 != currCourse.time_end) ||
                    (currCourse.time_end7 != null && currCourse.time_end7 != currCourse.time_end) ||
                    (currCourse.time_end8 != null && currCourse.time_end8 != currCourse.time_end) ||
                    (currCourse.time_end9 != null && currCourse.time_end9 != currCourse.time_end) ||
                    (currCourse.time_end10 != null && currCourse.time_end10 != currCourse.time_end) ||
                    (currCourse.time_end11 != null && currCourse.time_end11 != currCourse.time_end) ||
                    (currCourse.time_end12 != null && currCourse.time_end12 != currCourse.time_end)) {
                    variableDates = true;
                }
                var anmeldeLink = '<a onclick="' + anmeldeFunction + '" href="#book-course" class="booking-button">' + anmeldeText + '</a>';
                h.push('<div class="mobile-course-box kbs-clearfix">');
                h.push('	<ul>');
                h.push('		<li>Kurs: ' + currLevel.course_format_name + ' ' + currLevel.course_level_name + '</li>');
                h.push('		<li>Wochentag: ' + cm.General.getGermanWeekday(currCourse.weekday) + '</li>');
                h.push('		<li>Zeitraum: ' + course_date + not_on + '</li>');
                if (!variableDates) {
                    h.push('		<li>Uhrzeit: ' + currCourse.time_begin + ' - ' + currCourse.time_end + '</li>');
                } else {
                    h.push('		<li>Uhrzeit: ' + currCourse.time_begin + ' - ' + currCourse.time_end + '</br>Uhrzeit Variabel</li>');
                }
                h.push('		<li>Studio: ' + currCourse.location_name + '</li>');
                h.push('		<li>Trainerin: ' + trainer + '</li>');
                h.push('	</ul>');
                h.push('	<div class="booking-button-wrapper kbs-clearfix">' + anmeldeLink + '</div>');
                h.push('</div>');
            }
            return h.join('');
        },
        initSlideToggle: function () {
            jQuery('#course-container').unbind('click').on('click', '.course-level-head', function () {
                jQuery(this).parent().find('.course-level-content').slideToggle(cmProperties.toggleDuration, function () {
                });
            });
        }
    },
    BookingForm: {
        initForm: function () {
            var divContainer = jQuery('#booking-form-container');
            divContainer.empty();
            var h = [];
            var checkedPropCode = '';
            if (cmProperties.isNewCustomer) checkedPropCode = 'checked';
            var termsLinkUrl = '/agb/';
            var termsLink = '<a href="' + termsLinkUrl + '" target="_blank">AGBs gelesen und akzeptiert</a>';
            h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Zurück</a></div>');
            h.push('<div id="booking-form-box">');
            // h.push('<span style="color: green; font-weight: bold; font-style: italic">Attention! Work in progress.</span>');
            h.push('	<h3>Anmeldung für</h3>');
            h.push('	<ul>');
            h.push('		<li><span id="kbs-info-course-name"></span></li>');
            h.push('		<li>Kurs-Nr: <span id="kbs-info-course-number"></span></li>');
            h.push('		<li><span id="kbs-info-begin-text">Beginn</span>: <span id="kbs-info-begin-with-weekday"></span></li>');
//			h.push('		<li>Datum: <span id="kbs-info-date"></span></li>');
            h.push('		<li id="wrapper-kbs-info-date-listing">Termine: <span id="kbs-info-date-listing"></span></li>');
            h.push('		<li id="kbs-info-not-on-wrapper" style="display: none;">entfällt am: <span id="kbs-info-not-on"></span></li>');
            h.push('		<li id="wrapper-kbs-info-time">Uhrzeit: <span id="kbs-info-time"></span></li>');
            h.push('		<li>Trainer: <span id="kbs-info-trainer"></span></li>');
            h.push('		<li>Ort: <span id="kbs-info-location"></span></li>');
            h.push('		<li>Normalpreis   : <span id="kbs-info-price"></span> €</li>');
            h.push('		<li>Mitgliederpreis: <span id="kbs-info-memberprice"></span> €</li>');
            h.push('		<li id="wrapper-kbs-info-precondition"><br><b>Voraussetzung</b>: <span id="kbs-info-precondition"></span></li>');
            h.push('		<li id="wrapper-kbs-textblock"><br>Anmerkung: <span id="kbs-info-textblock"></span></li>');
            h.push('	</ul>');
            h.push('	<form id="form-booking">');
            h.push('		<input type="hidden" name="id" id="kbs-course-id" />');
            h.push('		<input type="hidden" name="code" id="kbs-registration-code" />');
            h.push('		<div class="kbs-clearfix">');
            h.push('			<input type="checkbox" name="new-customer" id="kbs-new-customer" ' + checkedPropCode + ' />');
            h.push('			<label for="kbs-new-customer">Ich bin Neukunde</label>');
            h.push('		</div>');
            h.push('		<div id="new-customer-part">');
            h.push('		</div>');
            h.push('		<label for="kbs-email">Email*</label>');
            h.push('		<input type="email" name="email" id="kbs-email" required />');
            h.push('		<div class="kbs-clearfix">');
            h.push('                    <input style="margin-top: 5px;" type="checkbox" name="terms-accepted" id="kbs-terms-accepted" required />');
            h.push('                    <label for="kbs-terms-accepted"> ' + termsLink + '</label>');
            h.push('		</div>');
            h.push('		<div class="kbs-clearfix">');
            h.push('		<input style="margin-top: 5px;" type="checkbox" name="newsletter" id="kbs-newsletter" checked>');
            h.push('		<label for="kbs-newsletter">Newsletter</label>');
            h.push('		</div>');
            h.push('		<div id="kbs-button-bar" class="kbs-clearfix">');
            h.push('			<div id="wrapper-submit-booking-button" class="kbs-clearfix">');
            h.push('				<input type="submit" class="booking-button" id="submit-booking-button" />');
            h.push('			</div>');
            h.push('			<div id="wrapper-go-back-button" class="kbs-clearfix">');
            h.push('				<a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >X</a>');
            h.push('			</div>');
            h.push('		</div>');
            h.push('		<div style="font-size: 11px; text-align: center; margin-top: 25px;">');
            // h.push('		<a href="https://www.breitschopf.cc" target="_blank">Kursbuchungssytem von Breitschopf IT Solutions</a>');
            h.push('		</div>');
            h.push('	</form>');
            h.push('</div>');

            h.push("<div id='dialog-message' title='Download complete'><p id='dialog-message-content'></p></div>");


            divContainer.html(h.join(''));


            if (cmProperties.isNewCustomer) cm.BookingForm.fillNewCustomerPart();
        },
        removeNewCustomerPart: function () {
            jQuery('#new-customer-part').empty();
        },
        fillNewCustomerPart: function () {
            h = [];
            h.push('			<label for="kbs-prename">Vorname*</label>');
            h.push('			<input name="prename" id="kbs-prename" required />');
            h.push('			<label for="kbs-surname">Nachname*</label>');
            h.push('			<input name="surname" id="kbs-surname" required />');
            h.push('			<label for="kbs-phone">Telefonnummer*</label>');
            h.push('			<input name="phone" style="margin-bottom: 0px;" id="kbs-phone" required />');
            h.push('            <p style="font-size: small;margin: 0px 0px 8px 0px;">für kurzfristige Kurszusagen oder Änderungen </p>');
            jQuery('#new-customer-part').html(h.join(''));
        },
        resetForm: function () {
            // jQuery('#kbs-info-course-name').html('');
            // jQuery('#kbs-info-date').html('');
            // jQuery('#kbs-info-time').html('');
            // jQuery('#kbs-info-trainer').html('');
            // jQuery('#kbs-info-location').html('');
            // jQuery('#kbs-info-price').html('');
            // jQuery('#kbs-info-memberprice').html('');
            // jQuery('#kbs-course-id').val('');
            // jQuery('#kbs-registration-code').val('');
            // jQuery('#submit-booking-button').val('BUCHEN');
        },
        openBookingForm: function (id, code) {
            cm.BookingForm.loadDetails(id, code);

        },
        translateEnglishWeekdaysToGerman: function (text) {
            text = text.replace(new RegExp('Sunday', 'g'), 'So')
                .replace(new RegExp('Monday', 'g'), 'Mo')
                .replace(new RegExp('Tuesday', 'g'), 'Di')
                .replace(new RegExp('Wednesday', 'g'), 'Mi')
                .replace(new RegExp('Thursday', 'g'), 'Do')
                .replace(new RegExp('Friday', 'g'), 'Fr')
                .replace(new RegExp('Saturday', 'g'), 'Sa');

            return text;
        },
        finishOpeningBookingForm: function (result) {
            console.log(result);
            var res = JSON.parse(result);
            if (res.error != 1) {
                if (res.data.length == 0) {
                    alert("Die Anmeldefrist dieses Kurses hat noch nicht begonnen oder ist bereits abgelaufen.");
                } else {
                    cm.BookingForm.initForm();
                    var d = res.data[0];
                    cm.BookingForm.resetForm();
                    var all_times_are_equal = cm.BookingForm.callculateAllTimesAreEqual(d);
                    var date_listing;
                    if (all_times_are_equal) {
                        date_listing = d.date_listing;
                    } else {
                        date_listing = '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date1 + d.time_end;
                        if (d.date2 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date2 + ' ' + d.time_end2;
                        }
                        if (d.date3 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date3 + ' ' + d.time_end3;
                        }
                        if (d.date4 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date4 + ' ' + d.time_end4;
                        }
                        if (d.date5 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date5 + ' ' + d.time_end5;
                        }
                        if (d.date6 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date6 + ' ' + d.time_end6;
                        }
                        if (d.date7 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date7 + ' ' + d.time_end7;
                        }
                        if (d.date8 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date8 + ' ' + d.time_end8;
                        }
                        if (d.date9 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date9 + ' ' + d.time_end9;
                        }
                        if (d.date10 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date10 + ' ' + d.time_end10;
                        }
                        if (d.date11 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date11 + ' ' + d.time_end11;
                        }
                        if (d.date12 != null) {
                            date_listing += '<br>&nbsp;&nbsp;&nbsp;&nbsp;' + d.date12 + ' ' + d.time_end12;
                        }

                        date_listing = cm.BookingForm.translateEnglishWeekdaysToGerman(date_listing);
                        jQuery('#wrapper-kbs-info-time').hide();

                    }

                    var trainer = d.trainer_name1;
                    if (d.trainer_name2 != null) trainer += ' & ' + d.trainer_name2;
                    var booking_button_text;
                    if (d.free_places_count <= 0) {
                        booking_button_text = 'BUCHEN (Warteliste)';
                    } else {
                        if (d.free_places_count == 1) {
                            places_text = '1 Platz frei';
                        } else {

                            places_text = d.free_places_count + ' Plätze frei';
                        }
                        booking_button_text = 'BUCHEN (' + places_text + ')';
                    }
                    jQuery('#kbs-info-course-name').html(d.course_format_name + ' ' + d.course_level_name);
                    jQuery('#kbs-info-course-number').html(d.course_id);

                    jQuery('#kbs-info-begin-with-weekday').html(d.begin_with_weekday);

                    if (d.begin == d.end) {
                        jQuery('#kbs-info-begin-text').html('Datum');
                        jQuery('#wrapper-kbs-info-date-listing').hide();
                    }
//					jQuery('#kbs-info-date').html(d.begin + ' - ' + d.end);
                    jQuery('#kbs-info-date-listing').html(date_listing);
                    if (d.not_on == null || d.not_on == '') {
                        jQuery('#kbs-info-not-on-wrapper').hide();
                    } else {
                        jQuery('#kbs-info-not-on').html(d.not_on);
                        jQuery('#kbs-info-not-on-wrapper').show();
                    }
                    if (d.precondition == null || d.precondition.trim == '') {
                        jQuery('#wrapper-kbs-info-precondition').hide();
                    } else {
                        jQuery('#kbs-info-precondition').html(d.precondition);
                        jQuery('#wrapper-kbs-info-precondition').show();
                    }
                    if (d.textblock_mode != null && d.textblock_mode.trim() == 1 && d.textblock != null && d.textblock.trim() != '') {
                        jQuery('#kbs-info-textblock').html(d.textblock);
                        jQuery('#wrapper-kbs-textblock').show();
                    } else {
                        jQuery('#wrapper-kbs-textblock').hide();
                    }

                    jQuery('#kbs-info-time').html(d.time_begin + ' - ' + d.time_end);
                    jQuery('#kbs-info-trainer').html(trainer);
                    jQuery('#kbs-info-location').html(d.location_name);
                    jQuery('#kbs-info-price').html(d.price);
                    jQuery('#kbs-info-memberprice').html(d.member_price);
                    jQuery('#kbs-course-id').val(d.course_id);
                    jQuery('#kbs-registration-code').val(d.registration_code);
                    jQuery('#submit-booking-button').val(booking_button_text);
                    var externSections = '.elementor-element-xih143b, .elementor-element-89z3yj1, .elementor-element-fwyh5jr';
                    jQuery(externSections).hide();
                    jQuery('#course-container').hide();
                    jQuery('#booking-form-container').show();
                }

            } else {
                alert(res.errtxt);
            }
        },

        callculateAllTimesAreEqual: function (d) {

            if ((d.time_begin == d.time_begin2 || d.time_begin2 == null) &&
                (d.time_begin == d.time_begin3 || d.time_begin3 == null) &&
                (d.time_begin == d.time_begin4 || d.time_begin4 == null) &&
                (d.time_begin == d.time_begin5 || d.time_begin5 == null) &&
                (d.time_begin == d.time_begin6 || d.time_begin6 == null) &&
                (d.time_begin == d.time_begin7 || d.time_begin7 == null) &&
                (d.time_begin == d.time_begin8 || d.time_begin8 == null) &&
                (d.time_begin == d.time_begin9 || d.time_begin9 == null) &&
                (d.time_begin == d.time_begin10 || d.time_begin10 == null) &&
                (d.time_begin == d.time_begin11 || d.time_begin11 == null) &&
                (d.time_begin == d.time_begin12 || d.time_begin12 == null) &&

                (d.time_end == d.time_end2 || d.time_end2 == null) &&
                (d.time_end == d.time_end3 || d.time_end3 == null) &&
                (d.time_end == d.time_end4 || d.time_end4 == null) &&
                (d.time_end == d.time_end5 || d.time_end5 == null) &&
                (d.time_end == d.time_end6 || d.time_end6 == null) &&
                (d.time_end == d.time_end7 || d.time_end7 == null) &&
                (d.time_end == d.time_end8 || d.time_end8 == null) &&
                (d.time_end == d.time_end9 || d.time_end9 == null) &&
                (d.time_end == d.time_end10 || d.time_end10 == null) &&
                (d.time_end == d.time_end11 || d.time_end11 == null) &&
                (d.time_end == d.time_end12 || d.time_begin12 == null)

            ) {
                return true;
            } else {
                return false;
            }
        },

        loadDetails: function (id, code) {
            var cmd = 'Courses.GetDetails';
            var parameter = {'course_id': id, 'registration_code': code};
            jQuery.post(cm.General.getAjaxUrl(cmd), parameter, function (result) {
                cm.BookingForm.finishOpeningBookingForm(result);
            });
        },
        goBackToCourses: function () {
            var externSections = '.elementor-element-xih143b, .elementor-element-89z3yj1, .elementor-element-fwyh5jr';
            jQuery(externSections).show();
            jQuery('#booking-form-container').hide();
            jQuery('#voucher-form-container').hide();
            jQuery('#course-container').show();
        },
        showNonBookingForm: function () {
            cm.BookingForm.goBackToCourses();
        }
    },
    CourseRegistration: {
        startSubmit: function () {
            if (cmProperties.processingSubmitStatus == true) {
                cm.General.resetProcessingSubmitStatus();
                return; // skip function
            }
            var cmd = 'Courses.SubmitRegistration';
            var request_data = cm.CourseRegistration.getRequestData();
            jQuery.post(cm.General.getAjaxUrl_v1(cmd), request_data, function (response_data) {
                var result;
                try {
                    result = JSON.parse(response_data);
                }
                catch (err) {
                    alert("Fehler 211 " + err + " " + response_data);
                    return null;
                }
                if (result.error != 1) {
                    var success_message = result.data;
                    cm.CourseRegistration.finishSubmit(success_message, result.info);
                } else {
                    alert(result.errtxt);
                }
            });
        },
        finishSubmit: function (success_message, info) {
            var email = jQuery('#kbs-email').val();
            jQuery('#form-booking').remove();
            jQuery('#booking-form-box ul').append('<br/><br/><li style="text-align: left;">' + success_message + '</li>');
            jQuery('#booking-form-box ul').append('<br/><br/>');
            jQuery('#booking-form-box ul').append('<div id="back-box" style="width: 100%" class="kbs-clearfix"><a class="booking-button" style="float:left; width: 100%" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Zurück</a></div>');
            if (info) {
                alert(info);
            }
        },
        getRequestData: function () {
            var request_data;
            var new_customer = (jQuery('#kbs-new-customer').is(':checked') ? 1 : 0);
            var terms_accepted = (jQuery('#kbs-terms-accepted').is(':checked') ? 1 : 0);
            if (new_customer) {
                request_data = {
                    'new_customer': 1,
                    'prename': jQuery('#kbs-prename').val(),
                    'surname': jQuery('#kbs-surname').val(),
                    'phone': jQuery('#kbs-phone').val(),
                    'email': jQuery('#kbs-email').val(),
                    'terms_accepted': terms_accepted,
                    'course_id': jQuery('#kbs-course-id').val(),
                    'registration_code': jQuery('#kbs-registration-code').val(),
                    'newsletter': jQuery('#kbs-newsletter').is(":checked")
                };
            } else {
                request_data = {
                    'new_customer': 0,
                    'email': jQuery('#kbs-email').val(),
                    'terms_accepted': terms_accepted,
                    'course_id': jQuery('#kbs-course-id').val(),
                    'registration_code': jQuery('#kbs-registration-code').val(),
                    'newsletter': jQuery('#kbs-newsletter').is(":checked")
                };
            }
            return request_data;
        },
        post: function () {
            cmProperties.processingSubmitStatus = true;
            cm.General.delayedResettingOfProcessingSubmitStatus();
        },
    },
    Membership: {
        initForm: function () {
            cm.General.initUrl();
            var divContainer = jQuery('#container-submit-membership');
            divContainer.empty();
            var h = [];
            var termsLinkUrl = '/agb-mitgliedschaft/';
            var termsLink = '<a href="' + termsLinkUrl + '" target="_blank">Mitgliedschafts-AGBs gelesen und akzeptiert</a>';
            h.push('<div id="membership-form-box">');
            h.push('	<h3>Anmeldung für die Mitgliedschaft</h3>');
            h.push('	<form id="form-membership">');
            h.push('		<label for="kbs-prename">Vorname*</label>');
            h.push('		<input name="prename" id="kbs-prename" required />');
            h.push('		<label for="kbs-surname">Nachname*</label>');
            h.push('		<input name="surname" id="kbs-surname" required />');
            h.push('		<label for="kbs-email">Email*</label>');
            h.push('		<input type="email" name="email" id="kbs-email" required />');
            h.push('		<label for="kbs-phone">Telefonnummer*</label>');
            h.push('		<input name="phone" id="kbs-phone" placeholder="+43..." required />');
            h.push('		<label for="kbs-birthday">Geburstdatum*</label>');
            h.push('		<input name="birthday" id="kbs-birthday" placeholder="z.B. 01.01.1990" required  />');
            h.push('		<label for="kbs-street">Straße und Nummer*</label>');
            h.push('		<input name="street" id="kbs-street" placeholder="z.B. Hauptstraße 1/1" required />');
            h.push('		<label for="kbs-zip">PLZ*</label>');
            h.push('		<input name="zip" id="kbs-zip" required />');
            h.push('		<label for="kbs-city">Ort*</label>');
            h.push('		<input name="city" id="kbs-city" required />');
            h.push('		<input type="checkbox" name="terms_accepted" id="kbs-terms-accepted" required />');
            h.push('		<label for="kbs-terms-accepted"> ' + termsLink + '</label>');
            h.push('		<input type="submit" class="submit-membership-button" id="submit-membership-button" />');
            h.push('	</form>');
            h.push('</div>');
            divContainer.html(h.join(''));
            divContainer.on('submit', 'form', function () {
                var cmd = 'Membership.Registration';
                var request_data = jQuery('#form-membership').serialize();
                var email = jQuery('#kbs-email').val();
                jQuery.post(cm.General.getAjaxUrl(cmd), request_data, function (response_data) {
                    var result = JSON.parse(response_data);
                    if (result.error != 1) {
                        var success_message = result.data;
                        jQuery('#form-membership').remove();
                        jQuery('#membership-form-box').append('<br/><br/><p>Vielen Dank für die Anmeldung zur Mitgliedschaft. Bitte den Mitgliedsbeitrag von 50 Euro überweisen an:<br/>Aerial Silk Vienna<br/>IBAN: AT732011182865639000<br/>BIC: GIBAATWWXXX<br/>Verwendungszweck: Member:' + email + '</p><br/><br/><br/>');
                    } else {
                        alert(result.errtxt);
                    }
                });
                return false;
            });
        }
    },
    Voucher: {
        openVoucherForm: function (amount, price) {
            var divContainer = jQuery('#voucher-form-container');
            divContainer.empty();
            var h = [];
            var termsLinkUrl = '/agb/';
            var termsLink = '<a href="' + termsLinkUrl + '" target="_blank">AGBs gelesen und akzeptiert</a>';
            h.push('<div id="back-box" class="kbs-clearfix"><a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Zurück</a></div>');
            h.push('<div id="booking-form-box">');
            h.push('	<h3>Open Silk Block Anfordern</h3>');
            h.push('	<ul>');
            h.push('		<li>Anzahl: ' + amount + '</li>');
            h.push('		<li>Preis: ' + price + ' €</li>');
            h.push('	</ul>');
            h.push('	<form id="form-voucher">');
            h.push('		<input type="hidden" name="amount" id="kbs-voucher-amount" value="'+ amount +'"/>');
            h.push('		<input type="hidden" name="price" id="kbs-voucher-price" value="'+ price +'"/>');
            h.push('		<label for="kbs-voucher-email">Email*</label>');
            h.push('		<input type="email" name="email" id="kbs-voucher-email" required />');
            h.push('		<div class="kbs-clearfix">');
            h.push('                    <input style="margin-top: 5px;" type="checkbox" name="terms-accepted" id="kbs-voucher-terms-accepted" required />');
            h.push('                    <label for="kbs-voucher-terms-accepted"> ' + termsLink + '</label>');
            h.push('		</div>');
            h.push('		<div id="kbs-button-bar" class="kbs-clearfix">');
            h.push('			<div id="wrapper-submit-booking-button" class="kbs-clearfix">');
            h.push('				<input type="submit" class="booking-button" id="submit-booking-button" />');
            h.push('			</div>');
            h.push('			<div id="wrapper-go-back-button" class="kbs-clearfix">');
            h.push('				<a class="booking-button" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >X</a>');
            h.push('			</div>');
            h.push('		</div>');
            h.push('	</form>');
            h.push('</div>');

            h.push("<div id='dialog-message' title='Download complete'><p id='dialog-message-content'></p></div>");

            divContainer.html(h.join(''));

            var externSections = '.elementor-element-xih143b, .elementor-element-89z3yj1, .elementor-element-fwyh5jr';
            jQuery(externSections).hide();
            jQuery('#course-container').hide();
            divContainer.show();
        },

        startSubmit: function () {
            if (cmProperties.processingSubmitStatus == true) {
                cm.General.resetProcessingSubmitStatus();
                return; // skip function
            }
            var cmd = 'Voucher.SubmitRequest';
            var request_data = cm.Voucher.getRequestData();
            jQuery.post(cm.General.getAjaxUrl_v1(cmd), request_data, function (response_data) {
                var result;
                try {
                    result = JSON.parse(response_data);
                }
                catch (err) {
                    alert("Fehler 211 " + err + " " + response_data);
                    return null;
                }
                if (result.error != 1) {
                    var success_message = result.data;
                    cm.Voucher.finishSubmit(success_message, result.info);
                } else {
                    alert(result.errtxt);
                }
            });
        },
        finishSubmit: function (success_message, info) {
            var email = jQuery('#kbs-email').val();
            jQuery('#form-voucher').remove();
            jQuery('#booking-form-box ul').append('<br/><br/><li style="text-align: left;">' + success_message + '</li>');
            jQuery('#booking-form-box ul').append('<br/><br/>');
            jQuery('#booking-form-box ul').append('<div id="back-box" style="width: 100%" class="kbs-clearfix"><a class="booking-button" style="float:left; width: 100%" href="#termine" onclick="cm.BookingForm.goBackToCourses();" id="go-back-button" >Zurück</a></div>');
            if (info) {
                alert(info);
            }
        },
        getRequestData: function () {
            var terms_accepted = (jQuery('#kbs-voucher-terms-accepted').is(':checked') ? 1 : 0);
            var request_data = {
                'voucher_amount': jQuery('#kbs-voucher-amount').val(),
                'voucher_price': jQuery('#kbs-voucher-price').val(),
                'email': jQuery('#kbs-voucher-email').val(),
                'terms_accepted': terms_accepted
            };

            return request_data;
        },
        post: function () {
            cmProperties.processingSubmitStatus = true;
            cm.General.delayedResettingOfProcessingSubmitStatus();
        }
    }
};
