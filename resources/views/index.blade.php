@extends('layout')

@section('meta_title', (!empty($page->meta_title)) ? $page->meta_title : $page->title)

@section('meta_description', (!empty($page->meta_description)) ? $page->meta_description : $page->title)

@section('content')

  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  <!-- Hero Area-->
  @if($slide_items->isNotEmpty())
    <div class="hero-area">
      <div class="hero-area-wrapper hero-slider-dots fix-slider-dots">
        <!-- Start Hero Slider Single -->
        @foreach($slide_items as $key => $slide_item)
          <div class="hero-area-single">
            <div class="hero-area-bg">
              <img class="hero-img" src="/img/slides/{{ $slide_item->image }}" alt="{{ $slide_item->title }}">
            </div>
            <div class="hero-content">
              <div class="container">
                <div class="row">
                  <div class="col-10 col-md-8 col-xl-6">
                    <h5 style="color:{{ $slide_item->color }};">{{ $slide_item->marketing }}</h5>
                    <h2 style="color:{{ $slide_item->color }};">{{ $slide_item->title }}</h2>
                    <p>{{ $slide_item->marketing }}</p>
                    <a href="{{ $slide_item->link }}" class="hero-button">Подробнее</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- Banner-->
  @if($discount_categories->isNotEmpty())
    <div class="banner-section section-top-gap-100">
      <!-- Start Banner Wrapper -->
      <div class="banner-wrapper">
        <div class="container">
          <div class="row">
            @foreach($discount_categories as $discount_category)
              <div class="col-lg-4 col-md-6 col-12">
                <!-- Start Banner Single -->
                <div class="banner-single">
                  <a href="/{{ $lang.'/'.$discount_category->slug .'/c-'. $discount_category->id }}" class="banner-img-link">
                    <img class="banner-img" src="/file-manager/{{ $discount_category->image }}" alt="{{ $discount_category->title }}">
                  </a>
                  <div class="banner-content">
                    <span class="banner-text-tiny">{{ $discount_category->title }}</span>
                    <h3 class="banner-text-large">{{ $discount_category->title_extra }}</h3>
                    <a href="/{{ $lang.'/'.$discount_category->slug .'/c-'. $discount_category->id }}" class="banner-link">Посмотреть</a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Product Tab-->
  <div class="product-tab-section section-top-gap-100">
    <!-- Start Section Content -->
    <div class="section-content-gap">
      <div class="container">
        <div class="row">
          <div class="section-content">
            <h3 class="section-title"><?php $mode_top_title = unserialize($mode_top->title); echo $mode_top_title[$lang]['title']; ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Tab Wrapper -->
    <div class="product-tab-wrapper">
      <div class="container">
        <div class="row no-gutters">
          @foreach($mode_top->products->where('status', 1)->take(8) as $top_product)
            <?php $product_lang = $top_product->products_lang->where('lang', $lang)->first(); ?>
            <div class="col-xl-3 col-sm-6 col-6">
              <div class="border-around">
                <div class="product-img-warp">
                  <a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$top_product->id }}" class="product-default-img-link">
                    <img src="/img/products/{{ $top_product->path.'/'.$top_product->image }}" alt="{{ $product_lang['title'] }}" class="product-default-img img-fluid">
                  </a>
                </div>
                <div class="product-default-content  product-default-single">
                  <h6 class="product-default-link"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$top_product->id }}">{{ $product_lang['title'] }}</a></h6>
                  <span class="product-default-price">{{ $product_lang['price'] }}₸</span>
                </div>
                <div class="product-actions">
                  <button class="btn-add-to-favorite <?php if (is_array($favorite) AND in_array($top_product->id, $favorite['products_id'])) echo 'color-red'; ?>" type="button" data-favourite-id="{{ $top_product->id }}" onclick="toggleFavourite(this);">
                    <i class="icon-heart"></i>
                  </button>

                  @if (is_array($items) AND isset($items['products_id'][$top_product->id]))
                    <a href="/{{ $lang }}/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>
                  @else
                    <button class="btn-add-to-cart" type="button" data-product-id="{{ $top_product->id }}" onclick="addToCart(this);" title="Добавить в корзину"><i class="icon-shopping-cart"></i> В корзину</button>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- Product Catagory-->
  <div class="product-catagory-section section-top-gap-100">
    <!-- Start Section Content -->
    <div class="section-content-gap">
      <div class="container">
        <div class="row">
          <div class="section-content">
            <h3 class="section-title">Популярные категории</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Catagory Wrapper -->
    <div class="product-catagory-wrapper">
      <div class="container">
        <div class="row">
          @foreach($relevant_categories as $relevant_category)
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
              <a href="/{{ $lang.'/'.$relevant_category->slug .'/c-'. $relevant_category->id }}" class="product-catagory-single">
                <div class="product-catagory-img">
                  <img src="/file-manager/{{ $relevant_category->image }}" alt="{{ $relevant_category->title }}">
                </div>
                <div class="product-catagory-content">
                  <h5 class="product-catagory-title">{{ $relevant_category->title }}</h5>
                  <span class="product-catagory-items">({{ $relevant_category->products->count() }} товаров)</span>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- Product Tab-->
  <div class="product-tab-section section-top-gap-100">
    <!-- Start Section Content -->
    <div class="section-content-gap">
      <div class="container">
        <div class="row">
          <div class="section-content">
            <h3 class="section-title"><?php $mode_new_title = unserialize($mode_new->title); echo $mode_new_title[$lang]['title']; ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Tab Wrapper -->
    <div class="product-tab-wrapper">
      <div class="container">
        <div class="row no-gutters">
          @foreach($mode_new->products->where('status', 1)->take(8) as $new_product)
            <?php $product_lang = $new_product->products_lang->where('lang', $lang)->first(); ?>
            <div class="col-xl-3 col-sm-6 col-6">
              <div class="border-around">
                <div class="product-img-warp">
                  <a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$new_product->id }}" class="product-default-img-link">
                    <img src="/img/products/{{ $new_product->path.'/'.$new_product->image }}" alt="{{ $product_lang['title'] }}" class="product-default-img img-fluid">
                  </a>
                </div>

                <div class="product-default-content  product-default-single">
                  <h6 class="product-default-link"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$new_product->id }}">{{ $product_lang['title'] }}</a></h6>
                  <span class="product-default-price">{{ $product_lang['price'] }}₸</span>
                </div>

                <div class="product-actions">
                  <button class="btn-add-to-favorite <?php if (is_array($favorite) AND in_array($new_product->id, $favorite['products_id'])) echo 'color-red'; ?>" type="button" data-favourite-id="{{ $new_product->id }}" onclick="toggleFavourite(this);">
                    <i class="icon-heart"></i>
                  </button>

                  @if (is_array($items) AND isset($items['products_id'][$new_product->id]))
                    <a href="/{{ $lang }}/cart" class="btn-go-to-cart"><i class="icon-shopping-cart"></i> Оформить</a>
                  @else
                    <button class="btn-add-to-cart" type="button" data-product-id="{{ $new_product->id }}" onclick="addToCart(this);" title="Добавить в корзину"><i class="icon-shopping-cart"></i> В корзину</button>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- Company Logo-->
  @if($companies->isNotEmpty())
    <div class="company-logo-section section-top-gap-100">
      <!-- Start Company Logo Wrapper -->
      <div class="company-logo-wrapper">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="company-logo-slider">
                @foreach ($companies as $company)
                  <div class="company-logo-single">
                    <a href="/brand/{{ $company->slug }}">
                      <img src="/img/companies/{{ $company->logo }}" alt="{{ $company->title }}" class="img-fluid company-logo-image">
                    </a>
                  </div>
                @endforeach
                <div class="company-logo-single">
                  <img src="assets/images/company_logo/company_logo_1.png" alt="" class="img-fluid company-logo-image">
                </div>
                <div class="company-logo-single">
                  <img src="assets/images/company_logo/company_logo_2.png" alt="" class="img-fluid company-logo-image">
                </div>
                <div class="company-logo-single">
                  <img src="assets/images/company_logo/company_logo_3.png" alt="" class="img-fluid company-logo-image">
                </div>
                <div class="company-logo-single">
                  <img src="assets/images/company_logo/company_logo_4.png" alt="" class="img-fluid company-logo-image">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

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