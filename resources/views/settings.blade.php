@extends('statamic::layout')

@section('content')
    <div class="flex items-center mb-3">
        <h1 class="flex-1">@lang('seotamic::general.seotamic_title')</h1>
    </div>
    <div class="flex items-center mb-3">
        <p>@lang('seotamic::general.seotamic_description')</p>
    </div>

    <div>
        <publish-form
                action="{{ cp_route('cnj.seotamic.update') }}"
                :blueprint='@json($blueprint)'
                :meta='@json($meta)'
                :values='@json($values)'
        />

    </div>
@stop
