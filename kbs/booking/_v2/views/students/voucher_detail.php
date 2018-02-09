<div class="title">
    <h2>Neuer OS Block</h2>
</div>
<div id="button-bar">
    <div id="button-go-back" onclick="cm.Students.backToStudent();" class="rb-button">Zurück zu Teilnehmer</div>
    <div id="button-go-back" onclick="cm.Students.backToVoucherList();" class="rb-button">Zurück zu OS Blöcken</div>
</div>
<form class="form-detail input-container clear-fix">
        <input type="hidden" name="v_id" id="v_id"/>
    <div class="clearfix">
        <div id="input-column1">
            <div class="input-row">
                <label for="v_title">Titel*:</label>
                <select name="v_title" id="v_title">
                    <option selected="selected">10er OS Block</option>
                    <option>20er OS Block</option>
                    <option>Sicherheitstraining</option>
                    <option>Mitgliedschaft</option>
                    <option>Sonstiges</option>
                </select>
            </div>
            <div class="input-row">
                <label for="v_amount">Menge*:</label>
                <input name="v_amount" type="text" id="v_amount" value="10">
            </div>
        </div>

    </div>
    <div id="button-bar2">
        <button class="button-save rb-button" onclick="cm.Students.saveVoucher();" type="button">Speichern</button>
        <!--        <button class="button-save-new rb-button" onclick="cm.Students.saveAndNew();" type="button">Speichern und neu-->
        </button>
    </div>
</form>


<script type="text/javascript">
    $("#v_title").change(function () {
        $("#v_title").find("option:selected").each(function () {
            switch ($(this).text()) {
                case "Sicherheitstraining":
                    $("#v_amount").val("1");
                    break;
                case "Mitgliedschaft":
                case "10er OS Block":
                    $("#v_amount").val("10");
                    break;
                case "20er OS Block":
                    $("#v_amount").val("20");
                    break;
                default:
                    $("#v_amount").val("0");
            }
        })
    });
</script>