@extends('joystick-admin.layout')

@section('content')

  @include('joystick-admin.partials.alerts')

  <br>
  <h1 class="text-center">AsSalamu alaikum {{ Auth::user()->name }}!</h1>
	<!-- <div class="row">
  	<img src="/img/joystick2.jpg" class="img-responsive center-block">
  </div> -->

@endsection