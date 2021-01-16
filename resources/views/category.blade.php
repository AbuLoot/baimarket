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
            <h3 class="breadcrumb-title">{{ $category->title }}: <strong>{{ $category->products->count() }}</strong></h3>
            <div class="breadcrumb-nav">
              <nav aria-label="breadcrumb">
                @foreach ($category->ancestors as $ancestor)
                  @unless($ancestor->parent_id != NULL && $ancestor->children->count() > 0)
                    <li class="breadcrumb-item">
                      <a href="/{{ $lang.'/'.$ancestor->slug.'/c-'.$ancestor->id }}">{{ $ancestor->title }}</a>
                      <svg class="breadcrumb-arrow" width="6px" height="9px"><use xlink:href="/img/sprite.svg#arrow-rounded-right-6x9"></use></svg>
                    </li>
                  @endunless
                @endforeach
                <li class="active">{{ $category->title }}</li>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ...:::: Start Shop Section:::... -->
  <div class="shop-section">
    <div class="container">
      <div class="row flex-column-reverse flex-lg-row-reverse">
        <div class="col-lg-3">

          <div class="siderbar-section">

            <form action="/catalog/{{ $category->slug }}" method="get" id="filter">
              {{ csrf_field() }}
              <?php $options_id = session('options'); ?>
              @foreach ($grouped as $data => $group)
                <div class="sidebar-single-widget">
                  <?php $data = unserialize($data); ?>
                  <h6 class="sidebar-title">{{ $data[$lang]['data'] }}</h6>
                  <div class="sidebar-content">
                    <div class="filter-type-select">
                      <ul>
                        @foreach ($group as $option)
                          <?php $titles = unserialize($option->title); ?>
                          <li>
                            <label class="checkbox-default" for="o{{ $option->id }}">
                              <input type="checkbox" id="o{{ $option->id }}" name="options_id[]" value="{{ $option->id }}" @if(isset($options_id) AND in_array($option->id, $options_id)) checked @endif>
                              <span>{{ $titles[$lang]['title'] }}</span>
                            </label>
                          </li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                </div>
              @endforeach
            </form>

          </div>
        </div>
        <div class="col-lg-9">
          <!-- Start Shop Product Sorting Section -->
          <div class="shop-sort-section">
            <div class="sort-box d-flex justify-content-between align-items-md-center align-items-start flex-md-row flex-column">
              <div class="sort-select-list">
                <form action="/catalog/{{ $category->slug }}" method="get" id="filter">
                  <fieldset>
                    <select name="speed" id="speed">
                      @foreach(trans('data.sort_by') as $key => $value)
                        <option value="{{ $key }}" @if($key == session('action')) selected @endif>По {{ Str::lower($value) }}</option>
                      @endforeach
                    </select>
                  </fieldset>
                </form>
              </div>

              <!-- Start Page Amount -->
              <div class="page-amount">
                <span>Показано с {{ $products->firstItem().' по '.$products->lastItem().' из '.$products->total() }} товаров</span>
              </div>
            </div>
          </div>
          <br>

          <div class="sort-product-tab-wrapper">
            <div class="row no-gutters">
              @foreach($products as $product)
                <?php $product_lang = $product->products_lang->where('lang', $lang)->first(); ?>
                <div class="col-xl-4 col-sm-6 col-6">
                  <!-- Start Product Defautlt Single -->
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
                        <a href="/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>
                      @else
                        <button class="btn-add-to-cart" type="button" data-product-id="{{ $product->id }}" onclick="addToCart(this);" title="Добавить в корзину"><i class="icon-shopping-cart"></i> В корзину</button>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Start Pagination -->
          {{ $products->links() }}

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