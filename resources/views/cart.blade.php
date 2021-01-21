@extends('layout')

@section('head')

@endsection

@section('content')
  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">Корзина</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="cart-section">
    <div class="cart-table-wrapper">
      <div class="container">

        @include('partials.alerts')

        <div class="table_desc">
          <div class="table_page table-responsive">
            <table>
              <thead>
                <tr>
                  <th class="product_thumb"></th>
                  <th class="product_name">Наименование</th>
                  <th class="product-price">Цена</th>
                  <th class="product_quantity">Количество</th>
                  <th class="product_total">Сумма</th>
                  <th class="product_remove">Удалить</th>
                </tr>
              </thead>
              <tbody>
                <?php $items = session('items'); ?>
                <?php $sum_total = 0; ?>
                @foreach ($products as $product)
                  <?php $product_lang = $product->products_lang->where('lang', $lang)->first(); ?>
                  <?php $quantity = $items['products_id'][$product->id]['quantity']; ?>
                  <?php $sum_total += $product_lang['price'] * $quantity; ?>
                  <tr>
                    <td class="product_thumb"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product->id }}"><img src="/img/products/{{ $product->path.'/'.$product->image }}"></a></td>
                    <td class="product_name"><a href="/{{ $lang.'/'.Str::limit($product_lang['slug'], 35).'/'.'p-'.$product->id }}">{{ $product_lang['title'] }}</a></td>
                    <td class="product-price">{{ $product_lang['price'] }}₸</td>
                    <td class="product_quantity">
                      <label>Кол.</label>
                      <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" onclick="decrement_quantity('{{ $product->id }}')">-</button>
                        <input type="text" class="form-control sum-{{ $product->id }}" oninput="input_quantity('{{ $product->id }}')" name="count[{{ $product->id }}]" id="input-quantity-{{ $product->id }}" data-price="{{ $product_lang['price'] }}" size="4" min="1" value="{{ $quantity }}">
                        <button class="btn btn-outline-secondary" type="button" onclick="increment_quantity('{{ $product->id }}')">+</button>
                      </div>
                    </td>
                    <td class="product_total"><span class="sum-item-{{ $product->id }}">{{ ($product_lang['price'] * $quantity) }}</span>₸</td>
                    <td class="product_remove"><a href="/{{ $lang }}/destroy-from-cart/{{ $product->id }}" onclick="return confirm('Удалить запись?')"><i class="fa fa-trash-o"></i></a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- <div class="cart_submit">
            <button type="submit">Обновить корзину</button>
          </div> -->
        </div>
      </div>
    </div>

    <!-- Start Coupon Start -->
    <div class="coupon_area">
      <div class="container">
        @if($products->isNotEmpty())
          <div class="row">
            <div class="col-lg-6 col-md-6">
              <div class="coupon_code right">
                <h3>Итого</h3>
                <div class="coupon_inner">
                  <div class="cart_subtotal">
                    <p>Общая сумма</p>
                    <p class="cart_amount"><i class="sum_total">{{ $sum_total }}</i>₸</p>
                  </div>
                  <div class="checkout_btn">
                    <a href="/{{ $lang }}/checkout">Оформить заказ</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @else
          <br>
          <h2>Корзина пуста</h2>
        @endif
      </div>
    </div>

  </div>

@endsection

@section('scripts')
<script>
  function increment_quantity(product_id) {

    var inputQuantityElement = $("#input-quantity-"+product_id);
    var newQuantity = parseInt($(inputQuantityElement).val()) + 1;
    addToCart(product_id, newQuantity);
  }

  function decrement_quantity(product_id) {

    var inputQuantityElement = $("#input-quantity-"+product_id);
    if ($(inputQuantityElement).val() > 1) {
      var newQuantity = parseInt($(inputQuantityElement).val()) - 1;
      addToCart(product_id, newQuantity);
    }
  }

  function input_quantity(product_id) {

    var inputQuantityElement = $("#input-quantity-"+product_id);
    var newQuantity = parseInt($(inputQuantityElement).val());
    addToCart(product_id, newQuantity);
  }

  function addToCart(product_id, new_quantity) {

    $.ajax({
      type: "get",
      url: '/{{ $lang }}/add-to-cart/'+product_id,
      dataType: "json",
      data: {
        "quantity": new_quantity
      },
      success: function(data) {
        var sum = parseInt(data.price) * data.quantity;

        $('.sum-'+product_id).val(data.quantity);
        $('.sum-item-'+product_id).text(sum);
        $('.sum_total').text(data.sumPriceItems);
      }
    });
  }
</script>
@endsection