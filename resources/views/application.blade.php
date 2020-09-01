@extends('layouts.app', ['title' => 'Денежная заявка', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <application :initial-data="{{ $data }}"></application>
@endsection
