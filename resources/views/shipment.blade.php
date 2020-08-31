@extends('layouts.app', ['title' => 'План поставки', 'subtitle' => '', 'subtitle_link' => ''])
@section('content')
  <shipment :initial-data="{{ $data }}"></shipment>
@endsection
