<script>

    $(document).ready(function () {
        $('.timepicker').pickatime({
            default: 'now', // Set default time: 'now', '1:30AM', '16:30'
            fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
            twelvehour: false, // Use AM/PM or 24-hour format
            donetext: '{{ trans('general.ok') }}', // text for done-button
            cleartext: '{{ trans('general.clear') }}', // text for clear-button
            canceltext: '{{ trans('general.cancel') }}', // Text for cancel-button
            autoclose: false, // automatic close timepicker
            ampmclickable: false, // make AM PM clickable
            aftershow: function(){} //Function for after opening timepicker
        });
    });

</script>