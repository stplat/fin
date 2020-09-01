@extends('layouts.app', ['title' => 'Форма №22', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <finance :initial-data="{{ $data }}"></finance>
@endsection
