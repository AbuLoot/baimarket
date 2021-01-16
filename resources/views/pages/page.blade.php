@extends('layout')

@section('head')

@endsection

@section('content')

  <!-- ...:::: Start Breadcrumb Section:::... -->
  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">{{ $page->title }}</h3>
            <div class="breadcrumb-nav">
              <nav aria-label="breadcrumb">
                <ul>
                  <li><a href="/">Главная</a></li>
                  <li class="active" aria-current="page">{{ $page->title }}</li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    {!! $page->content !!}
  </div>

@endsection
