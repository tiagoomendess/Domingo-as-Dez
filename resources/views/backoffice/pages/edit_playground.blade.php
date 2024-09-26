@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.playground') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.playground') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('playgrounds.update', ['playground' => $playground]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $playground->name) }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="input-field col s4 m3 l2">
                <input type="number" name="width" id="width" class="validate" value="{{ old('width', $playground->width) }}">
                <label for="width">{{ trans('models.width') }}</label>
            </div>

            <div class="input-field col s4 m3 l2">
                <input type="number" name="height" id="height" class="validate" value="{{ old('height', $playground->height) }}">
                <label for="height">{{ trans('models.height') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input type="number" name="capacity" id="capacity" class="validate" value="{{ old('capacity', $playground->capacity) }}">
                <label for="capacity">{{ trans('models.capacity') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input autocomplete="off" type="text" name="surface" id="surface" class="validate autocomplete" value="{{ old('surface', $playground->surface) }}" required>
                <label for="surface">{{ trans('models.surface') }}</label>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select id="club_id" name="club_id" class="browser-default">

                    @if($playground->club)

                        <option value="{{ $playground->club->id }}">{{ $playground->club->name }}</option>
                        @foreach(App\Club::all() as $club)

                            @if($club->id != $playground->club->id)
                                <option value="{{ $club->id }}">{{ $club->name }}</option>
                            @endif

                        @endforeach
                        <option value="">{{ trans('general.none') }}</option>

                    @else

                        <option value="">{{ trans('general.none') }}</option>
                        @foreach(App\Club::all() as $club)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endforeach

                    @endif

                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                @if($playground->picture)
                    <img class="materialboxed" src="{{ $playground->picture }}" alt="" style="width: 100%">
                @else

                    <div class="center">
                        <p class="small center">{{ trans('models.no_picture') }}</p>
                    </div>
                @endif

            </div>


            <div class="col s12 m4 l3">

                <h5 class="flow-text">{{ trans('general.edit') }} {{ trans('models.picture') }}</h5>
                <div class="divider"></div>

                <div class="file-field input-field">

                    <div class="btn">
                        <span>{{ trans('general.upload') }}</span>
                        <input name="picture" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" value="{{ $playground->picture }}">
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col s12 m8 l6">
                <div class="form-group">
                    <input type="hidden" name="address_latitude" id="address-latitude" value="{{ $latitude }}" />
                    <input type="hidden" name="address_longitude" id="address-longitude" value="{{ $longitude }}" />
                </div>
                <div id="address-map-container" style="width:100%;height:400px; ">
                    <div style="width: 100%; height: 100%" id="map"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m4 l3">
                <input required name="priority" id="priority" type="number" class="validate" value="{{ $playground->priority }}">
                <label for="priority">Prioridade</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs', $playground->obs) }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($playground->visible)
                            <input name="visible" type="checkbox" value="true" checked >
                        @else
                            <input name="visible" type="checkbox" value="true" >
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])
            </div>
        </div>

    </form>
@endsection

@section('scripts')

    <script>(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
        ({key: "{{ env('GOOGLE_MAP_KEY') }}", v: "weekly"});</script>

    <script src="/js/back/maps.js"></script>
    <script>
        $(function () {

            $('input.autocomplete').autocomplete({
                data: {
                    "Pelado": null,
                    "Relva Natural": null,
                    "Relva Artificial": null,
                    "Relva Hibrida": null,
                    "Taco": null,
                    "Cimento": null,
                    "Alcatrão": null,
                    "Cimento Polido": null,

                },
                limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
                onAutocomplete: function(val) {
                    // Callback function when value is autcompleted.
                },
                minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
            });
        })
    </script>
@endsection
