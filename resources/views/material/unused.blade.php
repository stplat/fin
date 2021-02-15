@extends('layouts.app', ['title' => 'Невостребованные материалы', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <unused :initial-data="{{ $materials }}"></unused>
@endsection
