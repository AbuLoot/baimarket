<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('meta_title', 'BaiMarket - 1000 мелочей. Товары оптом по Казахстану!')</title>
  <meta name="description" content="@yield('meta_description', 'BaiMarket - 1000 мелочей. Товары оптом по Казахстану!')">
  <meta name="author" content="issayev.adilet@gmail.com">

  <!-- ::::::::::::::Favicon icon::::::::::::::-->
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="/img/favicon.png" type="image/png">

  <!-- ::::::::::::::All CSS Files here :::::::::::::: -->
  <!-- Vendor CSS -->
  <link rel="stylesheet" href="/assets/css/vendor/font-awesome.min.css">
  <link rel="stylesheet" href="/assets/css/vendor/plaza-icon.css">
  <link rel="stylesheet" href="/assets/css/vendor/jquery-ui.min.css">

  <!-- Plugin CSS -->
  <link rel="stylesheet" href="/assets/css/plugins/slick.css">
  <link rel="stylesheet" href="/assets/css/plugins/animate.min.css">
  <link rel="stylesheet" href="/assets/css/plugins/nice-select.css">
  <link href="/bower_components/typeahead.js/dist/typeahead.bootstrap.css" rel="stylesheet">

  <!-- Main CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/custom.css">

  @yield('head')

  @if($section_codes->firstWhere('slug', 'header-code'))
    {{ $section_codes->firstWhere('slug', 'header-code')->content }}
  @endif
  <!-- Use the minified version files listed below for better performance and remove the files listed above -->
  <!-- <link rel="stylesheet" href="/assets/css/vendor/vendor.min.css">
  <link rel="stylesheet" href="/assets/css/plugins/plugins.min.css">
  <link rel="stylesheet" href="/assets/css/style.min.css"> -->
</head>
<body>
  <?php
    $contacts = $section->firstWhere('slug', 'contacts');
    $data_phones = unserialize($contacts->data_1);
    $phones = explode('/', $data_phones['value']);
    $data_email = unserialize($contacts->data_2);
    $data_address = unserialize($contacts->data_3);
  ?>
  <header class="header-section d-lg-block d-none">

    <div class="header-center">
      <div class="container">
        <div class="row d-flex justify-content-between align-items-center">
          <div class="col-3">
            <!-- Logo Header -->
            <div class="header-logo">
              <a href="/"><img src="/img/logo-baimarket.png"></a>
            </div>
          </div>
          <div class="col-6">
            <!-- Start Header Search -->
            <div class="header-search">
              <form action="/{{ $lang }}/search" method="get">
                <div class="header-search-box default-search-style d-flex">
                  <input class="default-search-style-input-box border-around border-right-none typeahead-goods" type="search" name="text" placeholder="Поиск ..." required>
                  <button class="default-search-style-input-btn" type="submit"><i class="icon-search"></i></button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-3 text-right">
            <!-- Start Header Action Icon -->
            <?php
              $items = session('items');
              $favorite = session('favorite');
            ?>
            <ul class="header-action-icon">
              <li>
                <a href="/{{ $lang }}/favorite">
                  <i class="icon-heart"></i><span class="header-action-icon-item-count" id="count-favorite">{{ (is_array($favorite)) ? count($favorite['products_id']) : 0 }}</span>
                </a>
              </li>
              <li>
                <a href="/{{ $lang }}/cart">
                  <i class="icon-shopping-cart"></i><span class="header-action-icon-item-count" id="count-items">{{ (is_array($items)) ? count($items['products_id']) : 0 }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Bottom Area -->
    <div class="header-bottom sticky-header">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <!-- Header Main Menu -->
            <div class="main-menu">
              <nav>
                <ul>
                  <?php $traverse = function ($categories, $parent_slug = NULL) use (&$traverse, $lang) { ?>
                    <?php foreach ($categories as $category) : ?>
                      <?php if ($category->isRoot() && $category->descendants->count() > 0) : ?>
                        <li class="has-dropdown">
                          <a class="@if (Request::is($lang.'/'.$category->slug.'*')) active @endif main-menu-link" href="/{{ $lang.'/'.$category->slug.'/c-'.$category->id }}">{{ $category->title }} <i class="fa fa-angle-down"></i></a>
                          <ul class="sub-menu">
                            <?php $traverse($category->children, $category->slug.'/'); ?>
                          </ul>
                        </li>
                      <?php else : ?>
                        <li><a class="@if (Request::is($lang.'/'.$category->slug.'*')) active @endif" href="/{{ $lang.'/'.$category->slug.'/c-'.$category->id }}">{{ $category->title }}</a></li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php }; ?>
                  <?php $traverse($categories); ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ...:::: Start Mobile Header Section:::... -->
  <div class="mobile-header-section d-block d-lg-none">
    <!-- Start Mobile Header Wrapper -->
    <div class="mobile-header-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="mobile-header--left">
              <a href="/"><img src="/img/logo-baimarket.png"></a>
            </div>
            <div class="mobile-header--right">
              <a href="#mobile-menu-offcanvas" class="mobile-menu offcanvas-toggle">
                <span class="mobile-menu-dash"></span>
                <span class="mobile-menu-dash"></span>
                <span class="mobile-menu-dash"></span>
              </a>
            </div>
          </div>

          <div class="col-12">
            <!-- Mobile Search -->
            <div class="mobile-menu-center">
              <br>
              <form action="/{{ $lang }}/search" method="get">
                <div class="header-search-box default-search-style d-flex">
                  <input class="default-search-style-input-box border-around border-right-none typeahead-goods" type="search" name="text" placeholder="Search entire store here ..." required>
                  <button class="default-search-style-input-btn" type="submit"><i class="icon-search"></i></button>
                </div>
              </form>

              <br>

              <!-- Mobile Actions -->
              <ul class="mobile-action-icon">
                <li class="mobile-action-icon-item">
                  <a href="/{{ $lang }}/favorite" class="mobile-action-icon-link">
                    <i class="icon-heart"></i>
                    <span class="mobile-action-icon-item-count" id="count-favorite">{{ (is_array($favorite)) ? count($favorite['products_id']) : 0 }}</span>
                  </a>
                </li>
                <li class="mobile-action-icon-item">
                  <a href="/{{ $lang }}/cart" class="mobile-action-icon-link">
                    <i class="icon-shopping-cart"></i>
                    <span class="mobile-action-icon-item-count" id="count-items">{{ (is_array($items)) ? count($items['products_id']) : 0 }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Offcanvas Mobile Menu-->
  <div id="mobile-menu-offcanvas" class="offcanvas offcanvas-leftside offcanvas-mobile-menu-section">
    <!-- Start Offcanvas Header -->
    <div class="offcanvas-header text-right">
      <button class="offcanvas-close"><i class="fa fa-times"></i></button>
    </div>
    <!-- Start Offcanvas Mobile Menu Wrapper -->
    <div class="offcanvas-mobile-menu-wrapper">
      <!-- Start Mobile Menu Bottom -->
      <div class="mobile-menu-bottom">
        <!-- Start Mobile Menu Nav -->
        <div class="offcanvas-menu">
          <ul>
            <?php $traverse = function ($categories, $parent_slug = NULL) use (&$traverse, $lang) { ?>
              <?php foreach ($categories as $category) : ?>
                <?php if ($category->isRoot() && $category->descendants->count() > 0) : ?>
                  <li>
                    <a href="#"><span>{{ $category->title }}</span></a>
                    <ul class="mobile-sub-menu">
                      <?php $traverse($category->children, $category->slug.'/'); ?>
                    </ul>
                  </li>
                <?php else : ?>
                  <li><a href="/{{ $lang.'/'.$category->slug.'/c-'.$category->id }}">{{ $category->title }}</a></li>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php }; ?>
            <?php $traverse($categories); ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="offcanvas-overlay"></div>

  <!-- Content -->
  @yield('content')

  <!-- Widget contact buttons -->
  <div class="material-button-anim">
    <ul class="list-inline" id="options">
      <li class="option">
        <button class="material-button option2 bg-whatsapp" type="button">
          <a href="whatsapp://send?phone={{ $phones[0] }}" target="_blank">
            <!-- <span class="fa fa-whatsapp" aria-hidden="true"></span> -->
            <img src="/img/whatsapp.png">
          </a>
        </button>
      </li>
      <li class="option">
        <button class="material-button option3 bg-ripple" type="button">
          <a href="tel:{{ $phones[0] }}" target="_blank"><span class="fa fa-phone" aria-hidden="true"></span></a>
        </button>
      </li>
      <li class="option">
        <button class="material-button option4" type="button">
          <a href="mailto:{{ $data_email['value'] }}" target="_blank"><span class="fa fa-envelope" aria-hidden="true"></span></a>
        </button>
      </li>
    </ul>
    <button class="material-button material-button-toggle btnBg" type="button">
      <span class="fa fa-user" aria-hidden="true"></span>
      <span class="ripple btnBg"></span>
      <span class="ripple btnBg"></span>
      <span class="ripple btnBg"></span>
    </button>
  </div>

  <!-- ...:::: Start Footer Section:::... -->
  <footer class="footer-section section-top-gap-100">
    <!-- Start Footer Top Area -->
    <div class="footer-top section-inner-bg">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-5">
            <div class="footer-widget footer-widget-contact">
              <!-- Logo Header -->
              <div class="header-logo">
                <a href="/"><img src="/img/logo-baimarket.png"></a>
              </div><br>
              <div class="footer-contact">
                <div class="customer-support">
                  <div class="customer-support-icon">
                    <img src="/assets/images/icon/support-icon.png" alt="">
                  </div>
                  <div class="customer-support-text">
                    <span>Номера телефонов</span>
                    <a class="customer-support-text-phone" href="tel:{{ $phones[0] }}">{{ $phones[0] }}</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-7">
            <div class="footer-widget footer-widget-subscribe">
              <h3 class="footer-widget-title">Присоединяйся к нам</h3>
              <?php $soc_networks = $section->firstWhere('slug', 'soc-networks'); ?>
              {!! $soc_networks->content !!}
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="footer-widget footer-widget-menu">
              <h3 class="footer-widget-title">Information</h3>
              <div class="footer-menu">
                <ul class="footer-menu-nav">
                  <?php $traverse = function ($pages) use (&$traverse, $lang) { ?>
                    <?php foreach ($pages as $page) : ?>
                      <?php if ($page->isRoot() && $page->descendants->count() > 0) : ?>
                        <li>
                          <a href="/{{ $lang }}/i/{{ $page->slug }}">{{ $page->title }}</a>
                          <ul>
                            <?php $traverse($page->children, $page->slug.'/'); ?>
                          </ul>
                        </li>
                      <?php else : ?>
                        <li>
                          <a href="/{{ $lang }}/i/{{ $page->slug }}">{{ $page->title }}</a>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php }; ?>
                  <?php $traverse($pages); ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Start Footer Bottom Area -->
    <div class="footer-bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 col-md-6">
            <div class="copyright-area">
              <p class="copyright-area-text">Copyright © <?php echo date('Y') ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- material-scrolltop button -->
  <!-- <button class="material-scrolltop" type="button"></button> -->

  <!-- ::::::::::::::All JS Files here :::::::::::::: -->
  <!-- Global Vendor, plugins JS -->
  <script src="/assets/js/vendor/modernizr-3.11.2.min.js"></script>
  <script src="/assets/js/vendor/jquery-3.5.1.min.js"></script>
  <script src="/assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
  <script src="/assets/js/vendor/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/vendor/jquery-ui.min.js"></script>

  <!--Plugins JS-->
  <script src="/assets/js/plugins/slick.min.js"></script>
  <script src="/assets/js/plugins/material-scrolltop.js"></script>
  <script src="/assets/js/plugins/jquery.nice-select.min.js"></script>
  <script src="/assets/js/plugins/jquery.zoom.min.js"></script>

  <!-- Use the minified version files listed below for better performance and remove the files listed above -->
  <!-- <script src="/assets/js/vendor.min.js"></script> 
  <script src="/assets/js/plugins.min.js"></script> -->

  <script src="/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>
  <!-- Typeahead Initialization -->
  <script>
    jQuery(document).ready(function($) {
      // Set the Options for "Bloodhound" suggestion engine
      var engine = new Bloodhound({
        remote: {
          url: '/{{ $lang }}/search-ajax?text=%QUERY%',
          wildcard: '%QUERY%'
        },
        datumTokenizer: Bloodhound.tokenizers.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace
      });

      $(".typeahead-goods").typeahead({
        hint: true,
        highlight: true,
        minLength: 2
      }, {
        limit: 10,
        source: engine.ttAdapter(),
        displayKey: 'title',

        templates: {
          empty: [
            '<li>&nbsp;&nbsp;&nbsp;Ничего не найдено.</li>'
          ],
          suggestion: function (data) {
            return '<li><a href="/{{ $lang }}/admin/products/' + data.id + '/edit"><img class="list-img" src="/img/products/' + data.path + '/' + data.image + '"> ' + data.title + '<br><span>Код: ' + data.barcode + '</span></a></li>'
          }
        }
      });
    });
  </script>
  <!-- Main JS -->
  <script src="/assets/js/main.js"></script>

  @yield('scripts')

  @if($section_codes->firstWhere('slug', 'footer-code'))
    {{ $section_codes->firstWhere('slug', 'footer-code')->content }}
  @endif

</body>
</html>