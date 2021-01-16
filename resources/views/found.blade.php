@extends('layout')

@section('head')

@endsection

@section('content')

  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  <!-- ...:::: Start Breadcrumb Section:::... -->
  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">Результат по запросу: <strong>{{ $text }}</strong></h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...:::: Start Shop Section:::... -->
  <div class="shop-section">
    <div class="container">
      <div class="row flex-column-reverse flex-lg-row-reverse">
        <div class="col-lg-12">
          <br>

          <!-- Start Tab Wrapper -->
          <div class="sort-product-tab-wrapper">
            <div class="container">
              <div class="row">
                @foreach($products_lang as $product_lang)
                  <div class="col-xl-3 col-sm-6 col-6">
                    <!-- Start Product Defautlt Single -->
                    <div class="border-around">
                      <div class="product-img-warp">
                        <a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product_lang->product->id }}" class="product-default-img-link">
                          <img src="/img/products/{{ $product_lang->product->path.'/'.$product_lang->product->image }}" alt="{{ $product_lang['title'] }}" class="product-default-img img-fluid">
                        </a>
                      </div>
                      <div class="product-default-content  product-default-single">
                        <h6 class="product-default-link"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product_lang->product->id }}">{{ $product_lang['title'] }}</a></h6>
                        <span class="product-default-price">{{ $product_lang['price'] }}₸</span>
                      </div>
                      <div class="product-actions">
                        <button class="btn-add-to-favorite <?php if (is_array($favorite) AND in_array($product_lang->product_id, $favorite['products_id'])) echo 'color-red'; ?>" type="button" data-favourite-id="{{ $product_lang->product_id }}" onclick="toggleFavourite(this);">
                          <i class="icon-heart"></i>
                        </button>

                        @if (is_array($items) AND isset($items['products_id'][$product_lang->product_id]))
                          <a href="/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>
                        @else
                          <button class="btn-add-to-cart" type="button" data-product-id="{{ $product_lang->product_id }}" onclick="addToCart(this);" title="Добавить в корзину"><i class="icon-shopping-cart"></i> В корзину</button>
                        @endif
                      </div>
                    </div>
                  </div>
                  <?php unset($product); ?>
                @endforeach
              </div>
            </div>
          </div>

          {{ $products_lang->links() }}

          <!-- Start Pagination -->
          <div class="page-pagination text-center">
            <ul>
              <li><a href="#">Previous</a></li>
              <li><a class="active" href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">Next</a></li>
            </ul>
          </div>
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
        url: '/{{ $lang }}/add-to-cart/'+productId,
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