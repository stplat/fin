@extends('layouts.app', ['title' => 'Вовлечение', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <involvement :initial-data="{{ $data }}"></involvement>
@endsection
