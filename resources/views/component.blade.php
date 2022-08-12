@extends('layouts/app')
@section('title')
    @if(isset($title))
        {!! $title !!}
    @endif
@stop
@section('subtitle')
    @if(isset($subtitle))
        {!! $subtitle !!}
    @endif
@stop
@section('breadcrumb')
    @if(isset($breadcrumb))
        {!! $breadcrumb !!}
    @endif
@stop
@section('content')
    <{{ $component }}
        @if(isset($props))
            @foreach($props as $prop => $val)
                @if(is_object($val) || is_array($val))
                    :{{$prop}} = "{{ json_encode($val) }}"
                @else
                    {{$prop}} = "{{$val}}"
                @endif
            @endforeach
        @endif
    />
@stop
