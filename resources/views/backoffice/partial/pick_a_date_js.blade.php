<script>
    $(document).ready(function () {
        $('.datepicker').pickadate({
            format: 'yyyy-mm-dd',
            monthsFull: [
                '{{ trans('months.full.1') }}',
                '{{ trans('months.full.2') }}',
                '{{ trans('months.full.3') }}',
                '{{ trans('months.full.4') }}',
                '{{ trans('months.full.5') }}',
                '{{ trans('months.full.6') }}',
                '{{ trans('months.full.7') }}',
                '{{ trans('months.full.8') }}',
                '{{ trans('months.full.9') }}',
                '{{ trans('months.full.10') }}',
                '{{ trans('months.full.11') }}',
                '{{ trans('months.full.12') }}'
            ],
            monthsShort: [
                '{{ trans('months.short.1') }}',
                '{{ trans('months.short.2') }}',
                '{{ trans('months.short.3') }}',
                '{{ trans('months.short.4') }}',
                '{{ trans('months.short.5') }}',
                '{{ trans('months.short.6') }}',
                '{{ trans('months.short.7') }}',
                '{{ trans('months.short.8') }}',
                '{{ trans('months.short.9') }}',
                '{{ trans('months.short.10') }}',
                '{{ trans('months.short.11') }}',
                '{{ trans('months.short.12') }}'
            ],
            weekdaysShort: [
                '{{ trans('days.short.1') }}',
                '{{ trans('days.short.2') }}',
                '{{ trans('days.short.3') }}',
                '{{ trans('days.short.4') }}',
                '{{ trans('days.short.5') }}',
                '{{ trans('days.short.6') }}',
                '{{ trans('days.short.7') }}'
            ],
            selectMonths: true,
            selectYears: 3,
            today: '{{ trans('general.today') }}',
            clear: '{{ trans('general.clear') }}',
            close: '{{ trans('general.ok') }}',
            closeOnSelect: false
        });
    });
</script>