<?php
/**
 * Created by PhpStorm.
 * User: Jian Astrero
 * Date: 26/09/2018
 * Time: 4:46 AM
 */?>

@extends('jugger-api::master')

@section('body')
    @php
    // die(json_encode($models));
    @endphp
    <jugger-home-component :root-url="'{{ url('') }}'" :prop-models="'{{ json_encode($models) }}'"></jugger-home-component>
@endsection
