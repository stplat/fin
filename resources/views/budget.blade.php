@extends('layouts.app', ['title' => 'Бюджет затрат (мат+топ)'])
@section('content')
  <budget :initial-data="{{ $data }}"></budget>
@endsection
