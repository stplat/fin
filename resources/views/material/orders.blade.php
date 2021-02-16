@extends('layouts.app', ['title' => 'Заявки на перераспределение', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <order-material :initial-data="{{ $materials }}"></order-material>
@endsection
