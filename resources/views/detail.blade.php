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
            <h3 class="breadcrumb-title">{{ $product_lang->title }}</h3>
            <div class="breadcrumb-nav">
              <nav aria-label="breadcrumb">
                <ul>
                  <li><a href="/">Главная</a></li>
                  <li><a href="/{{ $lang.'/'.$product->category->slug.'/c'.$product->category->id }}">{{ $product->category->title }}</a></li>
                  <li class="active" aria-current="page">{{ $product_lang->title }}</li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Start Product Details Section -->
  <div class="product-details-section">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="product-details-gallery-area">
            <div class="product-large-image product-large-image-horaizontal">
              @if ($product->images != '')
                <?php $images = ($product->images == true) ? unserialize($product->images) : []; ?>
                @foreach ($images as $k => $image)
                  <div class="product-image-large-single zoom-image-hover">
                    <img src="/img/products/{{ $product->path.'/'.$images[$k]['image'] }}" alt="{{ $product->title }}">
                  </div>
                @endforeach
              @else
                <div class="product-image-large-single zoom-image-hover">
                  <img src="assets/images/products_images/aments_products_large_image_1.jpg" alt="">
                </div>
              @endif
            </div>
            <div class="product-image-thumb product-image-thumb-horizontal pos-relative">
              @if ($product->images != '')
                @foreach ($images as $k => $image)
                  <div class="zoom-active product-image-thumb-single">
                    <img class="img-fluid" src="/img/products/{{ $product->path.'/'.$images[$k]['present_image'] }}" alt="">
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="product-details-content-area">
            <!-- Start  Product Details Text Area-->
            <div class="product-details-text">
              <h4 class="title">{{ $product_lang->title }}</h4>
              <div class="price">{{ $product_lang->price }}₸</div>
              {!! $product_lang->characteristic !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Start Product Content Tab Section -->
  <div class="product-details-content-tab-section section-inner-bg section-top-gap-100">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="product-details-content-tab-wrapper">

            <!-- Start Product Details Tab Button -->
            <ul class="nav tablist product-details-content-tab-btn d-flex justify-content-center">
              <li>
                <a class="nav-link active" data-toggle="tab" href="#description">
                  <h5>Описание</h5>
                </a>
              </li>
            </ul>

            <!-- Start Product Details Tab Content -->
            <div class="product-details-content-tab">
              <div class="tab-content">
                <!-- Start Product Details Tab Content Singel -->
                <div class="tab-pane active show" id="description">
                  <div class="single-tab-content-item">{!! $product_lang->description !!}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...:::: Start Product  Section:::... -->
  <div class="product-section section-top-gap-100">
    <!-- Start Section Content -->
    <div class="section-content-gap">
      <div class="container">
        <div class="row">
          <div class="section-content">
            <h3 class="section-title">Еще товары</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Product Wrapper -->
    <div class="product-wrapper">
      <div class="container">
        <div class="row">
          @foreach($similar_products_lang as $similar_product_lang)
            <div class="col-xl-3 col-sm-4 col-6">
              <div class="border-around">
                <div class="product-img-warp">
                  <a href="/{{ $lang.'/'.Str::limit($similar_product_lang['slug'], 35).'/'.'p-'.$product->id }}" class="product-default-img-link">
                    <img src="/img/products/{{ $similar_product_lang->product->path.'/'.$similar_product_lang->product->image }}" alt="{{ $similar_product_lang['title'] }}" class="product-default-img img-fluid">
                  </a>
                  <div class="product-action-icon-link">
                    <ul>
                      <li><a href="wishlist.html"><i class="icon-heart"></i></a></li>
                      <li><a href="#" data-toggle="modal" data-target="#modalAddcart"><i class="icon-shopping-cart"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="product-default-content  product-default-single">
                  <h6 class="product-default-link"><a href="/{{ $lang.'/'.Str::limit($similar_product_lang['slug'], 35).'/'.'p-'.$similar_product_lang->product->id }}">{{ $similar_product_lang['title'] }}</a></h6>
                  <span class="product-default-price">{{ $similar_product_lang['price'] }}₸</span>
                </div>
                <div class="product-actions">
                  <a href="#" class="btn-add-to-favorite"><i class="icon-heart"></i></a>
                  <a href="#" class="btn-add-to-cart"><i class="icon-shopping-cart"></i> В корзину</a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
  <script>
    // Add to cart
    function addToCart(i) {
      var productId = $(i).data("product-id");

      $.ajax({
        type: "get",
        url: '/add-to-cart/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>');
          $('#count-items').text(data.countItems);
          alert('Товар добавлен в корзину');
        }
      });
    }

    // Toggle favourite
    function toggleFavourite (f) {
      var productId = $(f).data("favourite-id");

      $.ajax({
        type: "get",
        url: '/toggle-favourite/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          if (data.status == true) {
            $('*[data-favourite-id="'+productId+'"]').addClass('color-red');
          } else {
            $('*[data-favourite-id="'+productId+'"]').removeClass('color-red');
          }
          $('#count-favorite').text(data.countFavorite);
        }
      });
    }
  </script>
@endsection