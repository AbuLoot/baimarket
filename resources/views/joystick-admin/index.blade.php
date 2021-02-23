@extends('joystick-admin.layout')

@section('content')

  @include('joystick-admin.partials.alerts')

  <br>
  <h1 class="text-center">AsSalamu alaikum<br> {{ Auth::user()->name }}!</h1>

  <div class="row">
    <img src="/file-manager/bg/bg-joystick-2.png" class="img-responsive center-block">
  </div>

@endsection