@extends('layouts.app', ['title' => 'Мой склад', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <warehouse :initial-data="{{ $data }}"></warehouse>
@endsection
