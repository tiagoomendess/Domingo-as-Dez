

$('#delete_request_form input[type=checkbox]').on('change', understandCheckboxChanged);

function understandCheckboxChanged() {

    var value = $('#delete_request_form input[type=checkbox]').is(":checked");
    var btn = $('#delete_request_form_submit');

    if (value) {
        btn.prop("disabled", false);
    } else {
        btn.prop("disabled", "disabled");
    }
    
}