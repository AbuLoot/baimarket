@extends('layout')

@section('meta_title', (!empty($page->meta_title)) ? $page->meta_title : $page->title)

@section('meta_description', (!empty($page->meta_description)) ? $page->meta_description : $page->title)

@section('content')

  <!-- ...:::: Start Breadcrumb Section:::... -->
  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">Contact Us</h3>
            <div class="breadcrumb-nav">
              <nav aria-label="breadcrumb">
                <ul>
                  <li><a href="index.html">Home</a></li>
                  <li class="active" aria-current="page">Contact Us</li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...::::Start Contact Section:::... -->
  <div class="contact-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <!-- Start Contact Details -->
          <div class="contact-details-wrapper section-top-gap-100-">
            <div class="contact-details">
              <!-- Start Contact Details Single Item -->
              <div class="contact-details-single-item">
                <div class="contact-details-icon">
                  <i class="fa fa-phone"></i>
                </div>
                <div class="contact-details-content contact-phone">
                  <a href="tel:+012345678102">+012 345 678 102</a>
                  <a href="tel:+012345678102">+012 345 678 102</a>
                </div>
              </div>
              <!-- Start Contact Details Single Item -->
              <div class="contact-details-single-item">
                <div class="contact-details-icon">
                  <i class="fa fa-globe"></i>
                </div>
                <div class="contact-details-content contact-phone">
                  <a href="mailto:urname@email.com">urname@email.com</a>
                  <a href="http://www.yourwebsite.com">www.yourwebsite.com</a>
                </div>
              </div>
              <!-- Start Contact Details Single Item -->
              <div class="contact-details-single-item">
                <div class="contact-details-icon">
                  <i class="fa fa-map-marker"></i>
                </div>
                <div class="contact-details-content contact-phone">
                  <span>Address goes here,</span>
                  <span>street, Crossroad 123.</span>
                </div>
              </div>
            </div>
            <!-- Start Contact Social Link -->
            <div class="contact-social">
              <h4>Follow Us</h4>
              <ul>
                <li><a href=""><i class="fa fa-facebook"></i></a></li>
                <li><a href=""><i class="fa fa-twitter"></i></a></li>
                <li><a href=""><i class="fa fa-youtube"></i></a></li>
                <li><a href=""><i class="fa fa-google-plus"></i></a></li>
                <li><a href=""><i class="fa fa-instagram"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="contact-form section-top-gap-100-">
            <h3>Get In Touch</h3>
            <form action="https://htmlmail.hasthemes.com/jaber/mail/contact.php" method="POST">
              <div class="row">
                <div class="col-lg-6">
                  <div class="default-form-box mb-20">
                    <label for="contact-name">Name</label>
                    <input type="text" id="contact-name" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="default-form-box mb-20">
                    <label for="contact-email">Email</label>
                    <input type="email" id="contact-email" required>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="default-form-box mb-20">
                    <label for="contact-subject">Subject</label>
                    <input type="text" id="contact-subject" required>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="default-form-box mb-20">
                    <label for="contact-message">Your Message</label>
                    <textarea id="contact-message" cols="30" rows="10"></textarea>
                  </div>
                </div>
                <div class="col-lg-12">
                  <button class="contact-submit-btn" type="submit">SEND</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection