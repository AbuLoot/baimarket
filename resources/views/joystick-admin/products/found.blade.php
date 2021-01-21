@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Продукты</h2>

  <h3>Поиск по запросу <b>"{{ $text }}"</b></h3>

  @include('joystick-admin.partials.alerts')

  <div class="row">
    <div class="col-md-6">
      <form action="/{{ $lang }}/admin/products-search" method="get">
        <div class="input-group input-search">
          <input type="search" class="form-control input-xs typeahead-goods" name="text" placeholder="Поиск...">

          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Категории <span class="caret"></span></button>
            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-category">
              <li><a href="/{{ $lang }}/admin/products"><b>Все продукты</b></a></li>
              <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $lang) { ?>
                <?php foreach ($nodes as $node) : ?>
                  <li><a href="/{{ $lang }}/admin/products-category/{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</a></li>
                  <?php $traverse($node->children, $prefix.'___'); ?>
                <?php endforeach; ?>
              <?php }; ?>
              <?php $traverse($categories); ?>
            </ul>
          </div>
        </div>
      </form><br>
    </div>

    <div class="col-md-6 text-right">
      <div class="btn-group">
        <button type="button" id="submit" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Функции <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" id="actions">
          @foreach(trans('statuses.data') as $num => $status)
            <li><a data-action="{{ $num }}" href="#">Статус {{ $status }}</a></li>
          @endforeach
          <li role="separator" class="divider"></li>
          @foreach($modes as $mode)
            <li><a data-action="{{ $mode->slug }}" href="#">Режим {{ $mode->title }}</a></li>
          @endforeach
        </ul>
      </div>
      <a href="/{{ $lang }}/admin/products/create" class="btn btn-success"><i class="material-icons md-18">add</i></a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-condensed table-hover">
      <thead>
        <tr class="active">
          <td><input type="checkbox" onclick="toggleCheckbox(this)" class="checkbox-ids"></td>
          <td>Картинка</td>
          <td>Название</td>
          <td>Компания</td>
          <td>Категория</td>
          <td>Номер</td>
          <td>Просмотры</td>
          <td>Язык</td>
          <td>Режим</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($products_lang as $product_lang)
          <tr>
            <td><input type="checkbox" name="products_id[]" value="{{ $product_lang->product->id }}" class="checkbox-ids"></td>
            <td><img src="/img/products/{{ $product_lang->product->path.'/'.$product_lang->product->image }}" class="img-responsive" style="width:80px;height:auto;"></td>
            <td>{{ $product_lang->title }}</td>
            <td>{{ (isset($product_lang->product->company->title)) ? $product_lang->product->company->title : '' }}</td>
            <td class="text-nowrap">{{ $product_lang->product->category->title }}</td>
            <td>{{ $product_lang->product->sort_id }}</td>
            <td>{{ $product_lang->views }}</td>
            <td>{{ $product_lang->lang }}</td>
            <td class="text-nowrap">
              @foreach ($product_lang->product->modes as $mode)
                <?php $mode = unserialize($mode->title); ?>
                {{ $mode[$lang]['title'] }}<br>
              @endforeach
            </td>
            <td class="text-info">{{ trans('statuses.data.'.$product_lang->product->status) }}</td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="/{{ $product_lang->lang }}/admin/products/{{ $product_lang->id }}/edit" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form class="btn-delete" method="POST" action="{{ route('products.destroy', [$lang, $product_lang->id]) }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $products_lang->links() }}

@endsection

@section('head')
  <link href="/bower_components/typeahead.js/dist/typeahead.bootstrap.css" rel="stylesheet">
@endsection

@section('scripts')
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

    // submit button click
    $("#actions > li > a").click(function() {

      var action = $(this).data("action");
      var productsId = new Array();

      $('input[name="products_id[]"]:checked').each(function() {
        productsId.push($(this).val());
      });

      if (productsId.length > 0) {
        $.ajax({
          type: "get",
          url: '/{{ $lang }}/admin/products-actions',
          dataType: "json",
          data: {
            "action": action,
            "products_id": productsId
          },
          success: function(data) {
            console.log(data);
            location.reload();
          }
        });
      }
    });

    function toggleCheckbox(source) {
      var checkboxes = document.querySelectorAll('input[type="checkbox"]');
      for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
          checkboxes[i].checked = source.checked;
      }
    }
  </script>
@endsection
