<label>{{ trans('general.timezone') }}</label>
<select id="timezone" name="timezone" class="browser-default" required>

    @if (isset($timezone_name) && isset($timezone_value))
        <option value="{{ $timezone_value }}" selected>{{ $timezone_name }}</option>
    @else
        <option disabled value="UTC" selected>{{ trans('general.selec_timezone') }}</option>
    @endif

    <option value="Europe/Lisbon">Europe/Lisbon</option>
    <option value="Europe/Paris">Europe/Paris</option>
    <option value="Europe/Berlin">Europe/Berlin</option>
    <option value="Europe/Moscow">Europe/Moscow</option>
    <option value="Atlantic/Azores">Atlantic/Azores</option>
    <option value="America/New_York">America/New_York</option>
    <option value="America/Chicago">America/Chicago</option>
    <option value="America/Denver">America/Denver</option>
    <option value="America/Los_Angeles">America/Los_Angeles</option>
    <option value="America/Sao_Paulo">America/Sao_Paulo</option>
    <option value="America/Manaus">America/Manaus</option>

</select>