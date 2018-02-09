var cmProperties = {
    ajaxServicesUrl: document.URL + "_v2/ajax/private-ajax-services.php",
    timerGetSearchResult: null
};
var cm = {
    General: {
        getAjaxUrl: function (cmd) {
            var time = new Date();
            time = time.getTime();
            var url = cmProperties.ajaxServicesUrl + "?cmd=" + cmd + "&t=" + time;
            return url;
        },
        getSymbolHtml: function (value) {
            if (value == 1) {
                return '<div class="status-active">✔</div>';
            } else {
                return '<div class="status-inactive">✖</div>';
            }
        },
        nullToEmpty: function (value) {
            if (!value) {
                return "";
            } else {
                return value;
            }
        },
        formatRegistrationStatus: function (value) {
            if (value) {
                switch (parseInt(value)) {
                    case 1:
                        return "<div style='color: orange; font-weight: bold'>Vorgemerkt</div>";
                        break;
                    case 2:
                        return "<div style='display:inline-block; color: #3399CC; font-weight: bold'>Angemeldet</div>";
                        break;
                    case 3:
                        return "<div style='color: green; font-weight: bold'>Bezahlt</div>";
                        break;
                    case 4:
                        return "<div style='color: orange; font-weight: bold'>Vorg. Wartel.</div>";
                        break;
                    case 5:
                        return "<div style='color: #49004a; font-weight: bold'>Warteliste</div>";
                        break;
                    case 6:
                        return "<div style='color: green; font-weight: bold'>Nachholer</div>";
                        break;
                    case 7:
                        return "<div style='color: green; font-weight: bold'>Sonstiges</div>";
                        break;
                    case 20:
                        return "<div style='color: gray; font-weight: bold'>Storno(abgel.)</div>";
                        break;
                    case 21:
                        return "<div style='color: gray; font-weight: bold'>Abgemeldet</div>";
                        break;
                    case 22:
                        return "<div style='color: green; font-weight: bold'>Drop-In</div>";
                        break;
                    case 23:
                        return "<div style='color: green; font-weight: bold'>Stundenübernahme</div>";
                        break;
                }
            }
            return "?"
        }
    },
    SelectableManager: {
        init: function (tableSelector) {
            $(tableSelector + ' tr').addClass('rb-selectable');
            $('.rb-selectable:not(:first-child)').on('click mousedown', function () {
                $('.ui-selected').toggleClass('ui-selected');
                $('.ui-selecting').toggleClass('ui-selecting');
                $(this).addClass('ui-selected ui-selecting');
            });
        },
        getSelectedId: function () {
            return $(".ui-selected, ui-selecting").first().attr('id');
        }
    },
    CourseFormats: {
        getSearchResult: function (delay) {
            cm.CourseFormats.getSearchResultAndNavigate(delay, false);
        },
        getSearchResultAndNavigate: function (delay, isNavigationRequired) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {

                var cmd = 'CourseFormats.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {
                        var h = [];
                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th>Sortierung</th>');
                        h.push('			<th style="min-width: 250px;">Name</th>');
                        h.push('			<th>Status</th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');
                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));
                        var da = d.data;
                        var length = da.length;
                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var statusHtml;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                if (curr.status == 1) {
                                    statusHtml = '<div class="status-active">✔</div>';
                                } else {
                                    statusHtml = '<div class="status-inactive">✖</div>';
                                }
                                h = [];
                                h.push('<tr id="' + curr.id + '">');
                                h.push('	<td class="td-center">' + curr.sort_no + '</td>');
                                h.push('	<td>' + curr.name + '</td>');
                                h.push('	<td class="td-center">' + statusHtml + '</td>');
                                h.push('</tr>');

                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                        cm.SelectableManager.init('.rb-table table');
                        cm.CourseFormats.initDblClick();

                        if (isNavigationRequired) {
                            $('.container-detail').hide();
                            $('.container-overview').show();
                        }
                    } else {
                        alert(d.errtxt);
                    }
                });
            }, delay);
        },
        new: function () {
            cm.CourseFormats.formReset();
            $('.container-overview').hide();
            $('.container-detail').show();
        },
        formReset: function () {
            $('#cf_id').val('');
            $('#cf_name').val('');
            $('#cf_sort_no').val(50);
            $('#cf_status').val(1);
        },
        formFilterReset: function () {
            $(".filter-form input[type='text']").val('');

            $(".filter-form select").each(function () {
                $(this).val($(this).find('option:first').val());
            });
        },
        backToOverview: function () {
            cm.CourseFormats.getSearchResultAndNavigate(0, true);
        },
        save: function () {
            var saveId = $('#cf_id').val();
            var cmd = 'CourseFormats.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    if (saveId == '') {
                        cm.CourseFormats.formFilterReset();
                    }
                    cm.CourseFormats.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        saveAndNew: function () {
            var cmd = 'CourseFormats.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.CourseFormats.formReset();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        edit: function () {
            var id = cm.SelectableManager.getSelectedId();
            cm.CourseFormats.editWidthId(id);
        },
        editWithId: function (id) {
            cm.CourseFormats.formReset();
            var cmd = 'CourseFormats.GetDetails';
            $.post(cm.General.getAjaxUrl(cmd), {'id': id}, function (data) {

                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#cf_id').val(da.id);
                    $('#cf_name').val(da.name);
                    $('#cf_sort_no').val(da.sort_no);
                    $('#cf_status').val(da.status);
                    $('.container-overview').hide();
                    $('.container-detail').show();
                } else {
                    alert(d.errtxt);
                }
            });


        },
        initDblClick: function () {
            $(".rb-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var $id = $(this).attr('id');
                cm.CourseFormats.editWithId($id);
            });
        }
    },
    CourseTypes: {
        getCourseFormatSelectOptions: function () {
            var cmd = 'CourseFormats.GetSearchResult';
            $.post(cm.General.getAjaxUrl(cmd), {'filter_status': '1'}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data;
                    var length = da.length;

                    $('.select_course_format').empty();
                    var curr;
                    var optionHtml;
                    optionHtml = '<option value=""></option>';
                    $('.select_course_format').append(optionHtml);
                    for (var i = 0; i < length; i++) {
                        curr = da[i];
                        optionHtml = '<option value="' + curr.id + '">' + curr.name + '</option>';
                        $('.select_course_format').append(optionHtml);
                    }
                } else {
                    alert(d.errtxt);
                }
            });
        },
        getSearchResult: function (delay) {
            cm.CourseTypes.getSearchResultAndNavigate(delay, false);
        },
        getSearchResultAndNavigate: function (delay, isNavigationRequired) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {

                var cmd = 'CourseTypes.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {

                        var h = [];

                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th>Sortierung</th>');
                        h.push('			<th style="min-width: 250px;">Name</th>');
                        h.push('			<th style="min-width: 250px;">Kursformat</th>');
                        h.push('			<th>Status</th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');

                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));

                        var da = d.data;
                        var length = da.length;

                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var statusHtml;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                if (curr.status == 1) {
                                    statusHtml = '<div class="status-active">✔</div>';
                                } else {
                                    statusHtml = '<div class="status-inactive">✖</div>';
                                }
                                h = [];
                                h.push('<tr id="' + curr.id + '">');
                                h.push('	<td class="td-center">' + curr.sort_no + '</td>');
                                h.push('	<td>' + curr.name + '</td>');
                                h.push('	<td>' + curr.course_format_name + '</td>');
                                h.push('	<td class="td-center">' + statusHtml + '</td>');
                                h.push('</tr>');

                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                        cm.SelectableManager.init('.rb-table table');
                        cm.CourseTypes.initDblClick();
                        if (isNavigationRequired) {
                            $('.container-detail').hide();
                            $('.container-overview').show();
                        }
                    } else {
                        alert(d.errtxt);
                    }
                });

            }, delay);
        },
        new: function () {
            cm.CourseTypes.formReset();
            $('.container-overview').hide();
            $('.container-detail').show();
        },
        formReset: function () {
            $('#ct_id').val('');
            $('#ct_name').val('');
            $('#ct_course_format').val('');
            $('#ct_is_kid_course').val('0');
            $('#ct_payment_type').val('1');
            $('#ct_sort_no').val(50);
            $('#ct_status').val(1);
        },
        formFilterReset: function () {
            $(".filter-form input[type='text']").val('');

            $(".filter-form select").each(function () {
                $(this).val($(this).find('option:first').val());
            });


        },
        backToOverview: function () {
            cm.CourseTypes.getSearchResultAndNavigate(0, true);
        },
        save: function () {
            var cmd = 'CourseTypes.Save';
            var saveId = $('#ct_id').val();
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    if (saveId == '') {
                        cm.CourseTypes.formFilterReset();
                    }
                    cm.CourseTypes.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        saveAndNew: function () {
            var cmd = 'CourseTypes.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.CourseTypes.formReset();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        edit: function () {
            var id = cm.SelectableManager.getSelectedId();
            cm.CourseTypes.editWidthId(id);
        },
        editWithId: function (id) {
            cm.CourseTypes.formReset();

            var cmd = 'CourseTypes.GetDetails';
            $.post(cm.General.getAjaxUrl(cmd), {'id': id}, function (data) {

                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#ct_id').val(da.id);
                    $('#ct_name').val(da.name);
                    $('#ct_course_format').val(da.course_format_id);
                    $('#ct_sort_no').val(da.sort_no);
                    $('#ct_is_kid_course').val(da.is_kid_course);
                    $('#ct_payment_type').val(da.payment_type);
                    $('#ct_status').val(da.status);
                    $('.container-overview').hide();
                    $('.container-detail').show();
                } else {
                    alert(d.errtxt);
                }
            });


        },
        initDblClick: function () {
            $(".rb-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var $id = $(this).attr('id');
                cm.CourseTypes.editWithId($id);
            });
        }
    },
    CourseLevels: {
        getCourseFormatSelectOptions: function () {
            cm.CourseTypes.getCourseFormatSelectOptions();
        },
        getCourseTypeSelectOptions: function () {
            var cmd = 'CourseTypes.GetSearchResult';
            $.post(cm.General.getAjaxUrl(cmd), {'filter_status': '1'}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data;
                    var length = da.length;

                    $('.select_course_type').empty();
                    var curr;
                    var optionHtml;
                    optionHtml = '<option value=""></option>';
                    $('.select_course_type').append(optionHtml);
                    for (var i = 0; i < length; i++) {
                        curr = da[i];
                        optionHtml = '<option course_format_id="' + curr.course_format_id +
                            '" value="' + curr.id + '">' + curr.name + '</option>';
                        $('.select_course_type').append(optionHtml);
                        $('.select_course_type option:not(:first-child)').hide();
                    }
                } else {
                    alert(d.errtxt);
                }
            });
        },
        getSearchResult: function (delay) {
            cm.CourseLevels.getSearchResultAndNavigate(delay, false);
        },
        getSearchResultAndNavigate: function (delay, isNavigationRequired) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {
                var cmd = 'CourseLevels.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {
                        var h = [];
                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th>Sortierung</th>');
                        h.push('			<th style="min-width: 230px;">Name</th>');
                        h.push('			<th style="min-width: 230px;">Kursart</th>');
                        h.push('			<th style="min-width: 230px;">Kursformat</th>');
                        h.push('			<th>Status</th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');
                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));
                        var da = d.data;
                        var length = da.length;
                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var statusHtml;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                if (curr.status == 1) {
                                    statusHtml = '<div class="status-active">✔</div>';
                                } else {
                                    statusHtml = '<div class="status-inactive">✖</div>';
                                }
                                h = [];
                                h.push('<tr id="' + curr.id + '">');
                                h.push('	<td class="td-center">' + curr.sort_no + '</td>');
                                h.push('	<td>' + curr.name + '</td>');
                                h.push('	<td>' + curr.course_type_name + '</td>');
                                h.push('	<td>' + curr.course_format_name + '</td>');
                                h.push('	<td class="td-center">' + statusHtml + '</td>');
                                h.push('</tr>');
                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                        cm.SelectableManager.init('.rb-table table');
                        cm.CourseLevels.initDblClick();
                        if (isNavigationRequired) {
                            $('.container-detail').hide();
                            $('.container-overview').show();
                        }
                    } else {
                        alert(d.errtxt);
                    }
                });

            }, delay);
        },
        new: function () {
            cm.CourseLevels.formReset();
            $('.container-overview').hide();
            $('.container-detail').show();
        },
        formReset: function () {
            $('#cl_id').val('');
            $('#cl_name').val('');
            $('#cl_course_format').val('');
            $('#cl_course_type').val('');
            $('#cl_units').val('');
            $('#cl_price').val('');
            $('#cl_member_price').val('');
            $('#cl_description').val('');
            $('#cl_sort_no').val(50);
            $('#cl_status').val(1);
            $('#cl_security_training').val(0);
            $('#cl_mail_reminder').val(0);
            $('#cl_voucher').val(0);
        },
        formFilterReset: function () {
            $(".filter-form input[type='text']").val('');

            $(".filter-form select").each(function () {
                $(this).val($(this).find('option:first').val());
            });
        },
        backToOverview: function () {
            cm.CourseLevels.getSearchResultAndNavigate(0, true);
        },
        save: function () {
            var cmd = 'CourseLevels.Save';
            var saveId = $('#cl_id').val();
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    if (saveId == '') {
                        cm.CourseLevels.formFilterReset();
                    }
                    cm.CourseLevels.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        saveAndNew: function () {
            var cmd = 'CourseLevels.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.CourseLevels.formReset();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        edit: function () {
            var id = cm.SelectableManager.getSelectedId();
            cm.CourseLevels.editWidthId(id);
        },
        editWithId: function (id) {
            var cmd = 'CourseLevels.GetDetails';
            cm.CourseLevels.formReset();
            $.post(cm.General.getAjaxUrl(cmd), {'id': id}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#cl_id').val(da.id);
                    $('#cl_name').val(da.name);
                    $('#cl_course_format').val(da.course_format_id);
                    $('#cl_course_type').val(da.course_type_id);
                    $('#cl_sort_no').val(da.sort_no);
                    $('#cl_units').val(da.units);
                    $('#cl_price').val(da.price);
                    $('#cl_member_price').val(da.member_price);
                    $('#cl_description').val(da.description);
                    $('#cl_status').val(da.status);
                    $('#cl_voucher').val(da.voucher);
                    $('#cl_mail_reminder').val(da.mail_reminder);
                    $('#cl_mail_reminder_hours').val(da.mail_reminder_hours);
                    $('#cl_security_training').val(da.security_training);


                    $('.container-overview').hide();
                    $('.container-detail').show();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        initDblClick: function () {
            $(".rb-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var $id = $(this).attr('id');
                cm.CourseLevels.editWithId($id);
            });
        }
    },
    Students: {
        getStudentSelectOptions: function () {
            cm.Students.getStudentSelectOptions();
        },
        getStudentSelectOptions: function () {
            var cmd = 'Students.GetSearchResult';
            $.post(cm.General.getAjaxUrl(cmd), {'filter_status': '1'}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data;
                    var length = da.length;
                    // !! //
                    $('.select_course_type').empty();
                    var curr;
                    var optionHtml;
                    optionHtml = '<option value=""></option>';
                    $('.select_course_type').append(optionHtml);
                    for (var i = 0; i < length; i++) {
                        curr = da[i];
                        optionHtml = '<option course_format_id="' + curr.course_format_id +
                            '" value="' + curr.id + '">' + curr.name + '</option>';
                        $('.select_course_type').append(optionHtml);
                        $('.select_course_type option:not(:first-child)').hide();
                    }
                } else {
                    alert(d.errtxt);
                }
            });
        },
        getSearchResult: function (delay) {
            cm.Students.getSearchResultAndNavigate(delay, false);
        },
        getSearchResultAndNavigate: function (delay, isNavigationRequired) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {
                var cmd = 'Students.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {
                        var h = [];
                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th style="min-width: 100px;">Vorname</th>');
                        h.push('			<th style="min-width: 230px;">Nachname</th>');
                        h.push('			<th style="min-width: 230px;">Email</th>');
                        h.push('			<th>Status</th>');
                        h.push('			<th>Newsletter</th>');
                        h.push('			<th>Mitgliedschaft</th>');
                        h.push('			<th style="font-size: 10px;">Sicherheitstraining</th>');
                        h.push('			<th style="font-size: 10px;">Mitgliedschaft<br/>beantragt am</th>');
                        h.push('			<th style="font-size: 10px;">Mitgliedschaft<br/>bezahlt bis</th>');
                        h.push('			<th style="min-width: 230px;">Teilnehmer- Vermerk</th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');
                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));
                        var da = d.data;
                        var length = da.length;
                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var statusHtml;
                            var newsletterHtml;
                            var securityTrainingHtml;
                            var membershipHtml;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                if (curr.mb_paid_date_formatted == null) curr.mb_paid_date_formatted = '';
                                if (curr.mb_application_date_formatted == null) curr.mb_application_date_formatted = '';
                                statusHtml = cm.General.getSymbolHtml(curr.status);
                                newsletterHtml = cm.General.getSymbolHtml(curr.newsletter);
                                securityTrainingHtml = cm.General.getSymbolHtml(curr.security_training);
                                membershipHtml = cm.General.getSymbolHtml(curr.membership);

                                h = [];
                                h.push('<tr id="' + curr.student_id + '">');
                                h.push('	<td class="td-center">' + curr.prename + '</td>');
                                h.push('	<td>' + curr.surname + '</td>');
                                h.push('	<td>' + curr.email + '</td>');
                                h.push('	<td class="td-center">' + statusHtml + '</td>');
                                h.push('	<td class="td-center">' + newsletterHtml + '</td>');
                                h.push('	<td class="td-center">' + membershipHtml + '</td>');
                                h.push('	<td class="td-center">' + securityTrainingHtml + '</td>');
                                h.push('	<td class="td-center">' + curr.mb_application_date_formatted + '</td>');
                                h.push('	<td class="td-center">' + curr.mb_paid_date_formatted + '</td>');
                                h.push('	<td>' + curr.student_remark + '</td>');
                                h.push('</tr>');
                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                        cm.SelectableManager.init('.rb-table table');
                        if (isNavigationRequired) {
                            $('.container-detail').hide();
                            $('.container-overview').show();
                        }
                        cm.Students.initDblClick();
                    } else {
                        alert(d.errtxt);
                    }
                });

            }, delay);
        },
        new: function () {
            cm.Students.formReset();
            $('.container-overview').hide();
            $('.container-detail').show();
        },
        formReset: function () {
            $('#st_id').val('');
            $('#st_surname').val('');
            $('#st_prename').val('');
            $('#st_email').val('');
            $('#st_phone').val('');
            $('#st_birthday').val('');
            $('#st_street').val('');
            $('#st_zip').val('');
            $('#st_city').val('');
            $('#st_newsletter').val(1);
            $('#st_remark').val('');
            $('#st_status').val(1);
            $('#st_membership').val(0);
            $('#st_mb_application').val('');
            $('#st_mb_begin').val('');
            $('#st_mb_paid_date').val('');
            $('#st_mb_end').val('');
            $('#security_training').val('');
        },
        formFilterReset: function () {
            $(".filter-form input[type='text']").val('');

            $(".filter-form select").each(function () {
                $(this).val($(this).find('option:first').val());
            });
        },
        backToOverview: function () {
            cm.Students.getSearchResultAndNavigate(0, true);
        },
        backToVoucherList: function () {
            this.showVouchers();
            $('.container-voucher-add').hide();
            $('.container-voucher').show();
        },
        backToStudent: function () {
            $('.container-courses').hide();
            $('.container-voucher').hide();
            $('.container-voucher-add').hide();
            $('.container-detail').show();

        },
        voucherAddFormReset: function () {
            $('#v_title').val('');
            $('#v_amount').val('');
        },
        save: function () {
            var cmd = 'Students.Save';
            var saveId = $('#st_id').val();
            //alert($('.form-detail').serialize());
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    if (saveId == '') {
                        cm.Students.formFilterReset();
                    }
                    cm.Students.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        saveAndNew: function () {
            var cmd = 'Students.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.Students.formReset();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        edit: function () {
            var id = cm.SelectableManager.getSelectedId();
            cm.Students.editWidthId(id);
        },
        editWithId: function (id) {
            cm.Students.formReset();
            var cmd = 'Students.GetDetails';
            $.post(cm.General.getAjaxUrl(cmd), {'student_id': id}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#st_id').val(da.student_id);
                    $('#st_surname').val(da.surname);
                    $('#st_prename').val(da.prename);
                    $('#st_email').val(da.email);
                    $('#st_phone').val(da.phone);
                    $('#st_birthday').val(da.birthday_formatted);
                    $('#st_street').val(da.street);
                    $('#st_zip').val(da.zip);
                    $('#st_city').val(da.city);
                    $('#st_newsletter').val(da.newsletter);
                    $('#st_student_remark').val(da.student_remark);
                    $('#st_status').val(da.status);
                    $('#st_membership').val(da.membership);
                    $('#st_mb_application').val(da.mb_application_formatted);
                    $('#st_mb_begin').val(da.mb_begin_formatted);
                    $('#st_mb_paid_date').val(da.mb_paid_date_formatted);
                    $('#st_mb_end').val(da.mb_end_formatted);
                    $('#security_training').val(da.security_training);

                    $('.container-overview').hide();
                    $('.container-detail').show();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        showCourses: function () {
            var cmd = 'Students.CourseList';
            $.post(cm.General.getAjaxUrl(cmd), $('#st_id').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var courseListContainer = $('#course-list');
                    courseListContainer.empty();
                    courseListContainer.append($('st_prename').val());
                    courseListContainer.append($('st_surname').val());

                    var h = [];
                    h.push('<table>');
                    h.push('	<tbody>');
                    h.push('		<tr>');
                    h.push('			<th>Kurs Nr.</th>');
                    h.push('			<th>Level</th>');
                    h.push('			<th>Typ</th>');
                    h.push('			<th>Format</th>');
                    h.push('			<th>Status</th>');
                    h.push('			<th>Anmeldungsnotizen</th>');
                    h.push('			 <th>Anmeldungsnotizen</br>(verborgen)</th>');
                    h.push('		</tr>');
                    h.push('	</tbody>');
                    h.push('</table>');
                    courseListContainer.append(h.join(""));

                    var da = d.data;
                    var length = da.length;
                    if (length == 0) {
                        $('#course-list table tbody').append('<br/>Keine Einträge gefunden.');
                    } else {
                        var curr;
                        for (var i = 0; i < length; i++) {
                            curr = da[i];

                            h = [];
                            h.push('<tr>');
                            h.push('	<td>' + curr.course_id + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.level_name) + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.type_name) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.format_name) + '</td>');
                            h.push('	<td>' + cm.General.formatRegistrationStatus(curr.status) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.public_remark) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.private_remark) + '</td>');
                            h.push('</tr>');
                            $('#course-list table tbody').append(h.join(""));
                        }
                    }

                    $('.container-detail').hide();
                    $('.container-courses').show();
                }
            });
        },
        showVouchers: function () {
            var cmd = 'Students.VoucherList';
            $.post(cm.General.getAjaxUrl(cmd), $('#st_id').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var voucherListContainer = $('#voucher-list');
                    voucherListContainer.empty();
                    voucherListContainer.append($('st_prename').val());
                    voucherListContainer.append($('st_surname').val());

                    var h = [];
                    h.push('<table class="voucher-list-table">');
                    h.push('	<tbody>');
                    h.push('		<tr>');
                    h.push('			<th>OS Block Nummer</th>');
                    h.push('			<th>Titel</th>');
                    h.push('			<th>Hinzugefügt</th>');
                    h.push('			<th>Anzahl</th>');
                    h.push('		</tr>');
                    h.push('	</tbody>');
                    h.push('</table>');
                    voucherListContainer.append(h.join(""));

                    var da = d.data;
                    var length = da.length;
                    if (length == 0) {
                        voucherListContainer.find('table tbody').append('<br/>Keine Einträge gefunden.');
                    } else {
                        var curr;
                        for (var i = 0; i < length; i++) {
                            curr = da[i];

                            h = [];
                            h.push('<tr id="' + curr.id + '">');
                            h.push('	<td>' + curr.id + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.title) + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.added) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.amount) + '</td>');
                            h.push('</tr>');
                            voucherListContainer.find('table tbody').append(h.join(""));
                        }
                    }

                    cm.SelectableManager.init('.voucher-list-table');
                    cm.Students.initDblClickVoucherList();
                    $('.container-detail').hide();
                    $('.container-voucher').show();
                    cm.Students.showVoucherUsed();
                }
            });
        },
        showVoucherUsed: function () {
            var cmd = 'Students.VoucherListUsed';
            $.post(cm.General.getAjaxUrl(cmd), $('#st_id').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var voucherListContainer = $('#voucher-list');
                    // voucherListContainer.empty();

                    var h = [];
                    h.push("<table id='used' style='margin-top:20px'>");
                    h.push('	<tbody>');
                    h.push('		<tr>');
                    h.push('			<th>Kurs Nr.</th>');
                    h.push('			<th>Level</th>');
                    h.push('			<th>Typ</th>');
                    h.push('			<th>Format</th>');
                    h.push('			<th>Status</th>');
                    h.push('			<th>Anmeldungsnotizen</th>');
                    h.push('			<th>Anmeldungsnotizen (verborgen)</th>');
                    h.push('			<th>OS Kurs</th>');
                    h.push('		</tr>');
                    h.push('	</tbody>');
                    h.push('</table>');
                    voucherListContainer.append(h.join(""));

                    var da = d.data;
                    var length = da.length;
                    if (length == 0) {
                        voucherListContainer.find('#used tbody').append('<br/>Keine Einträge gefunden.');
                    } else {
                        var curr;
                        for (var i = 0; i < length; i++) {
                            curr = da[i];

                            h = [];
                            h.push('<tr>');
                            h.push('	<td>' + curr.course_id + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.level_name) + '</td>');
                            h.push('	<td >' + cm.General.nullToEmpty(curr.type_name) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.format_name) + '</td>');
                            h.push('	<td>' + cm.General.formatRegistrationStatus(curr.status) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.public_remark) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.private_remark) + '</td>');
                            h.push('	<td>' + cm.General.nullToEmpty(curr.voucher) + '</td>');
                            h.push('</tr>');
                            voucherListContainer.find('#used tbody').append(h.join(""));
                        }
                    }

                }

            });
        },
        newVoucher: function () {
            cm.Students.voucherFormReset();
            $('.container-voucher').hide();
            $('.container-voucher-add').show();
        },
        saveVoucher: function () {
            var cmd = 'Students.SaveVoucher';
            var saveId = $('#st_id').val();
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    // if (saveId == '') {
                    //     // cm.Students.formFilterReset();
                    // }

                    cm.Students.voucherAddFormReset();
                    cm.Students.backToVoucherList();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        editVoucherWithId: function (id) {
            cm.Students.voucherFormReset();
            var cmd = 'Students.Voucher';
            $.post(cm.General.getAjaxUrl(cmd), {'voucher_id': id}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#v_id').val(da.id);
                    $('#v_title').val(da.title);
                    $('#v_amount').val(da.amount);
                    $('.container-voucher').hide();
                    $('.container-voucher-add').show();
                } else {
                    alert(d.errtxt);
                }

            });
        },
        voucherFormReset: function () {
            $('#v_id').val('');
            $('#v_title').val('');
            $('#v_amount').val('');
        },
        initDblClick: function () {
            $(".rb-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var id = $(this).attr('id');
                cm.Students.editWithId(id);
            });
        },
        initDblClickVoucherList: function () {
            $(".voucher-list-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var id = $(this).attr('id');
                cm.Students.editVoucherWithId(id);
            });
        }
    },
    Memberships: {
        getSearchResult: function (delay) {
            cm.Memberships.getSearchResultAndNavigate(delay, false);
        },
        getSearchResultAndNavigate: function (delay, isNavigationRequired) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {

                var cmd = 'Memberships.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {
                        var h = [];
                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th>Name</th>');
                        h.push('			<th>Email</th>');
                        h.push('			<th>Telefon</th>');
                        h.push('			<th>Registrierung</th>');
                        h.push('			<th>Status</th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');
                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));
                        var da = d.data;
                        var length = da.length;
                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var statusHtml;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                h = [];

                                if (curr.status == 1) {
                                    statusHtml = '<div class="status-active">❔</div>';
                                } else if (curr.status == 3) {
                                    statusHtml = '<div class="status-active">✔</div>';
                                } else {
                                    statusHtml = '<div class="status-inactive">✖</div>';
                                }

                                h.push('<tr id="' + curr.id + '">');
                                h.push('	<td>' + curr.name + '</td>');
                                h.push('	<td>' + curr.email + '</td>');
                                h.push('	<td>' + curr.phone + '</td>');
                                h.push('	<td>' + curr.registered + '</td>');
                                h.push('	<td class="td-center">' + statusHtml + '</td>');
                                h.push('</tr>');

                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                        cm.SelectableManager.init('.rb-table table');
                        cm.Memberships.initDblClick();

                        if (isNavigationRequired) {
                            $('.container-detail').hide();
                            $('.container-overview').show();
                        }
                    } else {
                        alert(d.errtxt);
                    }
                });
            }, delay);
        },
        new: function () {
            cm.Memberships.formReset();
            $('.container-overview').hide();
            $('.container-detail').show();
        },
        formReset: function () {
            $('#cf_id').val('');
            $('#cf_name').val('');
            $('#cf_sort_no').val(50);
            $('#cf_status').val(1);
        },
        formFilterReset: function () {
            $(".filter-form input[type='text']").val('');

            $(".filter-form select").each(function () {
                $(this).val($(this).find('option:first').val());
            });
        },
        backToOverview: function () {
            cm.Memberships.getSearchResultAndNavigate(0, true);
        },
        save: function () {
            var saveId = $('#mb_id').val();
            var cmd = 'Memberships.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    if (saveId == '') {
                        cm.Memberships.formFilterReset();
                    }
                    cm.Memberships.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        saveAndNew: function () {
            var cmd = 'Memberships.Save';
            $.post(cm.General.getAjaxUrl(cmd), $('.form-detail').serialize(), function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.Memberships.formReset();
                } else {
                    alert(d.errtxt);
                }
            });
        },
        edit: function () {
            var id = cm.SelectableManager.getSelectedId();
            cm.Memberships.editWidthId(id);
        },
        editWithId: function (id) {
            cm.Memberships.formReset();
            var cmd = 'Memberships.GetDetails';
            $.post(cm.General.getAjaxUrl(cmd), {'id': id}, function (data) {

                var d = JSON.parse(data);
                if (d.error != 1) {
                    var da = d.data[0];
                    $('#mb_id').val(da.id);
                    $('#m_id').val(da.mid);
                    $('#mb_prename').val(da.prename);
                    $('#mb_surname').val(da.surname);
                    $('#mb_email').val(da.email);
                    $('#mb_phone').val(da.phone);
                    $('#mb_street').val(da.street);
                    $('#mb_zip').val(da.zip);
                    $('#mb_city').val(da.city);
                    $('#mb_status').val(da.status);

                    if (da.mid) {
                        $("#is_member").text("Für diese Registrierung besteht bereits ein Account");
                        $("#convert").text("Eintragen und Daten übernehmen");
                    } else {
                        $("#is_member").text("Es wurde kein Account für diese Anmeldung gefunden");
                        $("#convert").text("Anlegen und Eintragen");
                    }

                    $('.container-overview').hide();
                    $('.container-detail').show();
                } else {
                    alert(d.errtxt);
                }
            });


        },
        initDblClick: function () {
            $(".rb-table  tr:not(:first-child)").on('dblclick taphold', function () {  // doubleclick and taphold
                var $id = $(this).attr('id');
                cm.Memberships.editWithId($id);
            });
        },
        convertToMember: function () {
            cm.Memberships.formReset();
            var cmd = 'Memberships.ConvertToMember';
            $.post(cm.General.getAjaxUrl(cmd), {'id': $('#mb_id').val()}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.Memberships.backToOverview();
                } else {
                    alert(d.errtxt);
                }
            });
        }
    },
    VoucherRequests: {
        getSearchResult: function (delay) {
            cm.VoucherRequests.getSearchResultAndNavigate(delay);
        },
        getSearchResultAndNavigate: function (delay) {
            cmProperties.timerGetSearchResult = null;
            cmProperties.timerGetSearchResult = setTimeout(function () {

                var cmd = 'VoucherRequests.GetSearchResult';
                $.post(cm.General.getAjaxUrl(cmd), $('.filter-form').serialize(), function (data) {
                    var d = JSON.parse(data);
                    if (d.error != 1) {
                        var h = [];
                        h.push('<table>');
                        h.push('	<tbody>');
                        h.push('		<tr>');
                        h.push('			<th>Name</th>');
                        h.push('			<th>Email</th>');
                        h.push('			<th>Block</th>');
                        h.push('			<th>Datum</th>');
                        h.push('			<th>Bezahlt</th>');
                        h.push('			<th></th>');
                        h.push('		</tr>');
                        h.push('	</tbody>');
                        h.push('</table>');
                        $('.rb-table').empty();
                        $('.rb-table').html(h.join(""));
                        var da = d.data;
                        var length = da.length;
                        if (length == 0) {
                            $('.rb-table').append('<br/>Keine Einträge gefunden.');
                        } else {
                            var curr;
                            var payedHtml;
                            var buttonText;
                            for (var i = 0; i < length; i++) {
                                curr = da[i];
                                h = [];

                                if (curr.payed == 1) {
                                    payedHtml = '<div class="status-active">✔</div>';
                                    buttonText = 'Storno';
                                } else {
                                    payedHtml = '<div class="status-inactive">✖</div>';
                                    buttonText = 'Bezahlt';
                                }

                                h.push('<tr id="' + curr.id + '">');
                                h.push('	<td>' + curr.name + '</td>');
                                h.push('	<td>' + curr.email + '</td>');
                                h.push('	<td>' + curr.title + '</td>');
                                h.push('	<td>' + curr.adddate + '</td>');
                                h.push('	<td class="td-center">' + payedHtml + '</td>');
                                h.push('	<td ><div id="button-change-payed" onclick="cm.VoucherRequests.acceptVoucher(' + curr.id + ')" class="rb-button" style="margin-bottom: 0px; margin-right: 0px;">' + buttonText + '</div></td>');
                                h.push('</tr>');

                                $('.rb-table table tbody').append(h.join(""));
                            }
                        }
                    } else {
                        alert(d.errtxt);
                    }
                });
            }, delay);
        },
        acceptVoucher: function (id) {
            var cmd = 'VoucherRequests.ChangeState';
            $.post(cm.General.getAjaxUrl(cmd), {'id': id}, function (data) {
                var d = JSON.parse(data);
                if (d.error != 1) {
                    cm.VoucherRequests.getSearchResult(0);
                } else {
                    alert(d.errtxt);
                }
            });
        }
    }
};
