@extends('layouts.app', ['title' => 'Бюджетные параметры', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <budget :initial-data="{{ $data }}"></budget>
@endsection
