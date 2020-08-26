<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <base href="{{ asset('') }}">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="keyword" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
{{--  <meta name="bearer" content="{{ auth()->user() ? auth()->user()->api_token : '' }}">--}}
  <title>{!! $title ?? '' !!}</title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon_16.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon_32.png') }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon_96.png') }}">
  <link href="{{ asset('css/app.css?=' . microtime(true)) }}" rel="stylesheet"/>
</head>
