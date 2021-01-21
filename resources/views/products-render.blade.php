
  <?php $items = session('items'); ?>
  <?php $favorite = session('favorite'); ?>

  @foreach($products as $product)
    <?php $product_lang = $product->products_lang->where('lang', $lang)->first(); ?>
    <div class="col-xl-4 col-sm-6 col-6">
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