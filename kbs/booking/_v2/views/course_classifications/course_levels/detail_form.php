<div class="title">
    <h2>Kurs-Ebenen-Details</h2>
</div>
<div id="button-bar">
    <div id="button-go-back" onclick="cm.CourseLevels.backToOverview();" class="rb-button">Zurück</div>
</div>
<form class="form-detail input-container clear-fix">
    <input type="hidden" name="id" id="cl_id"/>
    <div class="clearfix">
        <div id="input-column1">
            <div class="input-row">
                <label for="cl_name">Kurslevelname*:</label>
                <input name="name" type="text" id="cl_name">
            </div>
            <div class="input-row">
                <label for="cl_course_format">Kursformat*:</label>
                <select name='course_format_id' class="select_course_format" id='cl_course_format'>
                    <option value=''>Loading...</option>
                </select>
            </div>
            <div class="input-row">
                <label for="cl_course_type">Kursart*:</label>
                <select name='course_type_id' class="select_course_type" id='cl_course_type'>
                    <option value=''>Loading...</option>
                </select>
            </div>
            <div class="input-row">
                <label for="cl_units">Einheiten*:</label>
                <input name="units" type="text" id="cl_units">
            </div>
            <div class="input-row">
                <label for="cl_price">Preis*:</label>
                <input name="price" type="text" id="cl_price">
            </div>
            <div class="input-row">
                <label for="cl_member_price">Mitgliederpreis*:</label>
                <input name="member_price" type="text" id="cl_member_price">
            </div>
            <div class="input-row">
                <label for="cl_member_price">Beschreibung*:</label>
                <textarea name="description" id="cl_description"></textarea>
            </div>
            <div class="input-row">
                <label for="cl_sort_no">Sortierung*:</label>
                <input name="sort_no" type="text" id="cl_sort_no">
            </div>
            <div class="input-row">
                <label for="cl_status">Status*:</label>
                <select name='status' id='cl_status' class='select-with-symbols'>
                    <option value='1'>✔ aktiv</option>
                    <option value='0'>✖ deaktiviert</option>
                </select>
            </div>
            <div class="input-row">
                <label for="cl_voucher">OS Block Kurs:</label>
                <select name='voucher' id='cl_voucher' class='select-with-symbols'>
                    <option value='1'>✔ Ja</option>
                    <option value='0'>✖ Nein</option>
                </select>
            </div>
            <div class="input-row">
                <label for="cl_mail_reminder">Anmeldungs Erinnerung:</label>
                <select name='mail_reminder' id='cl_mail_reminder' class='select-with-symbols'>
                    <option value='0'>✖ deaktiviert</option>
                    <option value='1'>5 bezahlte</option>
                    <option value='2'>4 angemeldet</option>
                </select>
            </div>
            <div class="input-row">
                <label for="cl_mail_reminder_hours">Erinnerung nach (h):</label>
                <input name="mail_reminder_hours" type="text" id="cl_mail_reminder_hours">
            </div>
            <div class="input-row">
                <label for="cl_security_training">Sicherheitstraining erforderlich:</label>
                <select name='security_training' id='cl_security_training' class='select-with-symbols'>
                    <option value='1'>✔ Ja</option>
                    <option value='0'>✖ Nein</option>
                </select>
            </div>
        </div>

    </div>
    <div id="button-bar2">
        <button class="button-save rb-button" onclick="cm.CourseLevels.save();" type="button">Speichern</button>
        <button class="button-save-new rb-button" onclick="cm.CourseLevels.saveAndNew();" type="button">Speichern und
            neu
        </button>
    </div>
</form>