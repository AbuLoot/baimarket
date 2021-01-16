@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Страницы</h2>

  <p class="text-right">
    <a href="/{{ $lang }}/admin/pages/create" class="btn btn-success btn-sm">Добавить</a>
  </p>

  @include('joystick-admin.partials.alerts')

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>URI</td>
          <td>Номер</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, &$i) { ?>
          <?php foreach ($nodes as $page) : ?>
            <tr>
              <td>{{ $i++ }}</td>
              <td><a href="{{ url($page->slug) }}" target="_blank">{{ PHP_EOL.$prefix.' '.$page->title }}</a></td>
              <td>{{ $page->slug }}</td>
              <td>{{ $page->sort_id }}</td>
              <td>{{ $page->lang }}</td>
              @if ($page->status == 1)
                <td class="text-success">Активен</td>
              @else
                <td class="text-danger">Неактивен</td>
              @endif
              <td class="text-right text-nowrap">
                <a class="btn btn-link btn-xs" href="/{{ app()->getLocale() }}/admin/pages/{{ $page->id }}/edit" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
                <form class="btn-delete" method="POST" action="/{{ app()->getLocale() }}/admin/pages/{{ $page->id }}" accept-charset="UTF-8">
                  <input name="_method" type="hidden" value="DELETE">
                  <input name="_token" type="hidden" value="{{ csrf_token() }}">
                  <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
                </form>
              </td>
            </tr>
            <?php $traverse($page->children, $prefix.'__'); ?>
          <?php endforeach; ?>
        <?php }; ?>
        <?php $traverse($pages); ?>
      </tbody>
    </table>
  </div>
@endsection