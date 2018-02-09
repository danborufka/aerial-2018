<div class="title">
    <h2>OS Blöcke</h2>
</div>
<div id="button-bar">
    <div id="button-go-back" onclick="cm.Students.backToStudent();" class="rb-button">Zurück</div>
    <div id="button-new" onclick="cm.Students.newVoucher();" class="rb-button">Neu</div>
</div>
<div class='rb-table' id="voucher-list">
    Loading ...
</div>

<script type="text/javascript">
    $(document).ready(function () {
        function rb_init_selectable(p_element) {

            $(p_element + ' tr').addClass('rb-selectable');
            $('.rb-selectable:not(:first-child)').on('click mousedown', function () {

                $('.ui-selected').toggleClass('ui-selected');
                $('.ui-selecting').toggleClass('ui-selecting');
                $(this).addClass('ui-selected ui-selecting');

            });

        }

        var alt_key_is_pressed = false;

        $(window).keydown(function (event) {
            alt_key_is_pressed = true;
        });
        $(window).keyup(function (event) {
            alt_key_is_pressed = false;
        });

        function rb_get_selected_id() {
            return cm.SelectableManager.getSelectedId();
        }

        function rb_set_loading_effect() {
            $("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
        }

        $("form").submit(function () {   // loading effect
            rb_set_loading_effect();
        });

        rb_init_selectable(".voucher-list-table");
    };
</script