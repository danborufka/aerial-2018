<div class="title">
    <h2>Antrag Mitgliedschaft - Details</h2>
</div>
<div id="button-bar">
    <div id="button-go-back" onclick="cm.Memberships.backToOverview();" class="rb-button">Zurück</div>
</div>

<form class="form-detail input-container clear-fix">
    <input type="hidden" name="id" id="mb_id"/>
    <input type="hidden" name="mid" id="m_id"/>
    <div class="clearfix">
        <div id="input-column1">
            <div class="input-row">
                <label for="mb_prename">Vorname*:</label>
                <input name="prename" type="text" id="mb_prename">
            </div>
            <div class="input-row">
                <label for="mb_surname">Nachname*:</label>
                <input name="surname" type="text" id="mb_surname">
            </div>
            <div class="input-row">
                <label for="mb_email">Email*:</label>
                <input name="email" type="text" id="mb_email">
            </div>
            <div class="input-row">
                <label for="mb_phone">Telefon*:</label>
                <input name="phone" type="text" id="mb_phone">
            </div>
            <div class="input-row">
                <label for="mb_street">Straße*:</label>
                <input name="street" type="text" id="mb_street">
            </div>
            <div class="input-row">
                <label for="mb_zip">Postleitzahl*:</label>
                <input name="zip" type="text" id="mb_zip">
            </div>
            <div class="input-row">
                <label for="mb_city">Stadt*:</label>
                <input name="city" type="text" id="mb_city">
            </div>

            <div class="input-row">
                <label for="mb_status">Status*:</label>
                <select name='status' id='mb_status' class='select-with-symbols'>
                    <option value='1'>✔ Beworben</option>
                    <option value='0'>✖ Abgelehnt</option>
                    <option value='3'>✔ Übernommen</option>
                </select>
            </div>

            <div class="input-row">
                <span id="is_member"></span>
            </div>
        </div>
    </div>
    <div id="button-bar2">
        <button class="button-save rb-button" onclick="cm.Memberships.save();" type="button">Speichern</button>
        <button id="convert" class="button-commit rb-button" onclick="cm.Memberships.convertToMember();" type="button">Als Mitglied registrieren</button>
        <!--						<button class="button-save-new rb-button" onclick="cm.Memberships.saveAndNew();" type="button" >Speichern und neu</button>-->
    </div>
</form>