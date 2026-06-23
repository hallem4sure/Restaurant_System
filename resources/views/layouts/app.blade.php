@extends('adminlte::page')

@section('title')
    {{ config('app.name') }} - @yield('page_title', 'Dashboard')
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>@yield('page_title', 'Dashboard')</h1>
        </div>
        <div class="col-sm-6">
            @yield('breadcrumbs')
        </div>
    </div>
@stop

@section('content')
    @include('partials.alerts')
    @yield('main_content')
@stop

@section('css')
    @yield('custom_css')
@stop

@section('js')
    @yield('custom_js')
@stop
