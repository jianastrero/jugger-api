<?php
/**
 * Created by PhpStorm.
 * User: Jian Astrero
 * Date: 26/09/2018
 * Time: 4:46 AM
 */?>

@extends('jugger-api::master')

@section('body')
    <jugger-login-component :root-url="'{{ url('') }}'"></jugger-login-component>
@endsection
