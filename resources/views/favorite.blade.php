@extends('layout')

@section('meta_title', 'Избранные')

@section('meta_description', 'Избранные')

@section('content')

  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  <!-- ...:::: Start Breadcrumb Section:::... -->
  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">Избранные</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...:::: Start Shop Section:::... -->
  <div class="shop-section">
    <div class="container">
      <div class="row flex-column-reverse- flex-lg-row-reverse-">
        @foreach($products as $product)
          <?php $product_lang = $product->products_lang->where('lang', $lang)->first(); ?>
          <div class="col-xl-3 col-sm-6 col-6">
            <div class="border-around">
              <div class="product-img-warp">
                <a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product->id }}" class="product-default-img-link">
                  <img src="/img/products/{{ $product->path.'/'.$product->image }}" alt="{{ $product_lang['title'] }}" class="product-default-img img-fluid">
                </a>
              </div>
              <div class="product-default-content  product-default-single">
                <h6 class="product-default-link"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product->id }}">{{ $product_lang['title'] }}</a></h6>
                <span class="product-default-price">{{ $product_lang['price'] }}₸</span>
              </div>
              <div class="product-actions">
                <button class="btn-add-to-favorite <?php if (is_array($favorite) AND in_array($product->id, $favorite['products_id'])) echo 'color-red'; ?>" type="button" data-favourite-id="{{ $product->id }}" onclick="toggleFavourite(this);">
                  <i class="icon-heart"></i>
                </button>

                @if (is_array($items) AND isset($items['products_id'][$product->id]))
                  <a href="/{{ $lang }}/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>
                @else
                  <button class="btn-add-to-cart" type="button" data-product-id="{{ $product->id }}" onclick="addToCart(this);" title="Добавить в корзину"><i class="icon-shopping-cart"></i> В корзину</button>
                @endif
              </div>
            </div>
          </div>
        @endforeach
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
        url: '/{{ $lang }}/add-to-cart/'+productId,
        dataType: "json",
        data: {},
        success: function(data) {
          $('*[data-product-id="'+productId+'"]').replaceWith('<a href="/{{ $lang }}/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>');
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
        url: '/{{ $lang }}/toggle-favourite/'+productId,
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