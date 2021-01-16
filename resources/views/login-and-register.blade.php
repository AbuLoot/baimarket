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
            <h3 class="breadcrumb-title">Login</h3>
            <div class="breadcrumb-nav">
              <nav aria-label="breadcrumb">
                <ul>
                  <li><a href="index.html">Home</a></li>
                  <li><a href="shop-grid-sidebar-left.html">Shop</a></li>
                  <li class="active" aria-current="page">Login</li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...:::: Start Customer Login Section :::... -->
  <div class="customer_login">
    <div class="container">
      <div class="row">
        <!--login area start-->
        <div class="col-lg-6 col-md-6">
          <div class="account_form" data-aos="fade-up"  data-aos-delay="0">
            <h3>login</h3>
            <form method="POST" action="/{{ $lang }}/cs-login">
              @csrf
              <div class="default-form-box mb-20">
                <label>Username or email <span>*</span></label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email*" value="{{ old('email') }}" required>
              </div>
              <div class="default-form-box mb-20">
                <label>Passwords <span>*</span></label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password*" required>
              </div>
              <div class="login_submit">
                <button class="mb-20" type="submit">Войти</button>
                <label class="checkbox-default mb-20" for="offer">
                  <input type="checkbox" id="offer">
                  <span>Запомнить меня</span>
                </label>
                <a href="#">Забыли пароль?</a>
              </div>
            </form>
          </div>
        </div>

        <!--register area start-->
        <div class="col-lg-6 col-md-6">
          <div class="account_form register" data-aos="fade-up"  data-aos-delay="200">
            <h3>Register</h3>
            <form method="POST" action="/{{ $lang }}/cs-register">
              @csrf
              <div class="default-form-box mb-20">
                <label>Email address <span>*</span></label>
                <input type="text">
              </div>
              <div class="default-form-box mb-20">
                <label>Passwords <span>*</span></label>
                <input type="password">
              </div>
              <div class="login_submit">
                <button type="submit">Register</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection