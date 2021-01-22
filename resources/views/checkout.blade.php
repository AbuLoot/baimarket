@extends('layout')

@section('meta_title', 'Оформление заказа')

@section('meta_description', 'Оформление заказа')

@section('content')

  <div class="breadcrumb-section">
    <div class="breadcrumb-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12 d-flex justify-content-between justify-content-md-between  align-items-center flex-md-row flex-column">
            <h3 class="breadcrumb-title">Оформление заказа</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="checkout_section">
    <div class="container">

      @include('partials.alerts')

      <div class="checkout_form">
        <form action="/{{ $lang }}/store-order" method="post">
          @csrf
          <div class="row">
            <div class="col-lg-6 col-md-6">
              <h3>Данные покупателя</h3>
              <div class="row">
                <div class="col-lg-6 mb-20">
                  <div class="default-form-box">
                    <label>Фамилия <span>*</span></label>
                    <input type="text" minlength="2" maxlength="60" name="surname" placeholder="Фамилия*" value="{{ old('surname') }}" required>
                  </div>
                </div>
                <div class="col-lg-6 mb-20">
                  <div class="default-form-box">
                    <label>Имя <span>*</span></label>
                      <input type="text" minlength="2" maxlength="40" name="name" placeholder="Имя*" value="{{ old('name') }}" required>
                  </div>
                </div>
                <div class="col-12 mb-20">
                  <div class="default-form-box">
                    <label>Имя компании</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}">
                  </div>
                </div>
                <div class="col-12 mb-20">
                  <div class="default-form-box">
                    <label for="region_id">Регион <span>*</span></label>
                    <select class="country_option" name="region_id" id="region_id">
                      <?php $traverse = function ($nodes, $prefix = null) use (&$traverse) { ?>
                        <?php foreach ($nodes as $node) : ?>
                          <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
                          <?php $traverse($node->children, $prefix.'___'); ?>
                        <?php endforeach; ?>
                      <?php }; ?>
                      <?php $traverse($regions); ?>
                    </select>
                  </div>
                </div>
                <div class="col-12 mb-20">
                  <div class="default-form-box">
                    <label>Адрес улицы <span>*</span></label>
                    <input type="text" name="address" placeholder="Регион, город, улица, дом..." value="{{ old('phone') }}" required>
                  </div>
                </div>
                <div class="col-lg-6 mb-20">
                  <div class="default-form-box">
                    <label>Номер телефона<span>*</span></label>
                    <input type="tel" pattern="(\+?\d[- .]*){7,13}" name="phone" placeholder="Номер телефона*" value="{{ old('phone') }}" required>
                  </div>
                </div>
                <div class="col-lg-6 mb-20">
                  <div class="default-form-box">
                    <label>Email почта</label>
                    <input type="email" name="email" id="email" placeholder="bake_make@gmail.com" value="{{ old('email') }}">
                  </div>
                </div>
                <div class="col-12">
                  <div class="order-notes">
                    <label for="order_note">Комментарии к заказу</label>
                    <textarea id="order_note" name="notes" placeholder="Дополнительная информация"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <h3>Ваш заказ</h3>
              <div class="order_table">
                <table>
                  <thead>
                    <tr>
                      <th>Продукт</th>
                      <th>Итог</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $sum_total = 0; $sum_pr?>
                    <?php $items = session('items'); ?>
                    @foreach ($products as $product)
                      <?php $product_lang = $product->products_lang->where('lang', $lang)->first(); ?>
                      <?php $quantity = $items['products_id'][$product->id]['quantity']; ?>
                      <?php $sum_total += $product_lang['price'] * $quantity; ?>
                      <tr>
                        <td>
                          <input type="hidden" name="count[{{ $product->id }}]" value="{{ $quantity }}">
                          {{ $product_lang['title'] }} <strong> × {{ $quantity }}</strong>
                        </td>
                        <td><?php echo $product_lang['price'] * $quantity; ?>₸</td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="order_total">
                      <th>Общая сумма</th>
                      <td><strong>{{ $sum_total }}₸</strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="payment_method">
                <h4>Способ оплаты:</h4>
                @foreach(trans('orders.pay') as $id => $item)
                  <label class="radio-inline">
                    <input type="radio" name="pay" value="{{ $id }}" @if($id == 2) checked @endif> {{ $item['value'] }}
                  </label>
                @endforeach
                <div class="order_button pt-15">
                  <button type="submit">Оформить заказ</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection