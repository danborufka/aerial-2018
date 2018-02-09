<div class="title">
    <h2>Teilnehmer- Details</h2>
</div>
<div id="button-bar">
    <div id="button-go-back" onclick="cm.Students.backToOverview();" class="rb-button">Zurück</div>
    <div id="button-courses" onclick="cm.Students.showCourses();" class="rb-button">Kursliste</div>
    <div id="button-courses" onclick="cm.Students.showVouchers();" class="rb-button">OS Blöcke</div>
</div>
<form class="form-detail input-container clear-fix">
    <input type="hidden" name="student_id" id="st_id"/>
    <div class="clearfix">
        <div id="input-column1">
            <div class="input-row">
                <label for="st_prename">Vorname*:</label>
                <input name="prename" type="text" id="st_prename">
            </div>
            <div class="input-row">
                <label for="st_surname">Nachname*:</label>
                <input name="surname" type="text" id="st_surname">
            </div>
            <div class="input-row">
                <label for="st_email">Email*:</label>
                <input name="email" type="text" id="st_email">
            </div>
            <div class="input-row">
                <label for="st_phone">Telefonnummer:</label>
                <input name="phone" type="text" id="st_phone" placeholder="+43 ...">
            </div>
            <div class="input-row">
                <label for="st_birthday">Geburtsdatum:</label>
                <input name="birthday" type="text" id="st_birthday" placeholder="z.B. 01.01.1990">
            </div>
            <div class="input-row">
                <label for="st_street">Stra&szlig;e und Hausnummer:</label>
                <input name="street" type="text" id="st_street" placeholder="z.B. Hauptstraße 1/1">
            </div>
            <div class="input-row">
                <label for="st_zip">PLZ:</label>
                <input name="zip" type="text" id="st_zip">
            </div>
            <div class="input-row">
                <label for="st_city">Ort:</label>
                <input name="city" type="text" id="st_city">
            </div>
            <div class="input-row">
                <label for="st_newsletter">Newsletter*:</label>
                <select name='newsletter' class="select_student_newsletter select-with-symbols" id='st_newsletter'>
                    <option value='1'>✔ abonniert</option>
                    <option value='0'>✖ nicht abonniert</option>
                </select>
            </div>
            <div class="input-row">
                <label for="st_status">Status*:</label>
                <select name='status' id='st_status' class="select-with-symbols">
                    <option value='1'>✔ aktiv</option>
                    <option value='2'>✖ deaktiviert</option>
                    <option value='3'>➥ fusioniert</option>
                    <option value='4'>✖✖ gesperrt</option>
                    <option value='5'>@ unverifizierte Email</option>
                </select>
            </div>
            <div class="input-row">
                <label for="st_student_remark">Teilnehmer- Vermerk:</label>
                <textarea name="student_remark" id="st_student_remark" rows="3"></textarea>
            </div>
        </div>
        <div id="input-column2">
            <div class="input-row">
                <label for="st_membership">Mitgliedschaft:</label>
                <select name="membership" class="select_student_membership select-with-symbols" id="st_membership">
                    <option value='0'>✖ nein</option>
                    <option value='1'>✔ ja</option>
                </select>
            </div>
            <div class="input-row">
                <label for="st_mb_application">Antrag:</label>
                <input name="mb_application" type="text" id="st_mb_application" placeholder="z.B. 01.01.1990">
            </div>
            <div class="input-row">
                <label for="st_mb_begin">Beginn:</label>
                <input name="mb_begin" type="text" id="st_mb_begin" placeholder="z.B. 01.01.1990">
            </div>
            <div class="input-row">
                <label for="st_mb_paid_date">bezahlt bis:</label>
                <select name='mb_paid_date' id='st_mb_paid_date' class="select-with-symbols">
                    <option value=''></option>
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
            </div>
            <div class="input-row">
                <label for="st_mb_end">gekündigt am:</label>
                <input name="mb_end" type="text" id="st_mb_end" placeholder="z.B. 01.01.1990">
            </div>
            <div class="input-row">
                <label for="security_training">Sicherheitstraining:</label>
                <select name="security_training" class="select-with-symbols" id="security_training">
                    <option value='0'>✖ nein</option>
                    <option value='1'>✔ ja</option>
                </select>
            </div>
        </div>

    </div>
    <div id="button-bar2">
        <button class="button-save rb-button" onclick="cm.Students.save();" type="button">Speichern</button>
        <button class="button-save-new rb-button" onclick="cm.Students.saveAndNew();" type="button">Speichern und neu
        </button>
    </div>
</form>