@extends('joystick-admin.layout')

@section('content')

  <h2 class="page-header">Регионы</h2>

  @include('joystick-admin.partials.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/regions/create" class="btn btn-success btn-sm">Добавить</a>
  </p>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Название</td>
          <td>Slug</td>
          <td>Номер</td>
          <td>Язык</td>
          <td>Статус</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $lang, &$i) { ?>
          <?php foreach ($nodes as $node) : ?>
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ PHP_EOL.$prefix.' '.$node->title }}</td>
              <td>{{ $node->slug }}</td>
              <td>{{ $node->sort_id }}</td>
              <td>{{ $node->lang }}</td>
              @if ($node->status == 1)
                <td class="text-success">Активен</td>
              @else
                <td class="text-danger">Неактивен</td>
              @endif
              <td class="text-right">
                <a class="btn btn-link btn-xs" href="{{ route('regions.edit', [$lang, $node->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
                <form method="POST" action="{{ route('regions.destroy', [$lang, $node->id]) }}" accept-charset="UTF-8" class="btn-delete">
                  <input name="_method" type="hidden" value="DELETE">
                  <input name="_token" type="hidden" value="{{ csrf_token() }}">
                  <button type="submit" class="btn btn-link btn-xs" onclick="return confirm('Удалить запись?')"><i class="material-icons md-18">clear</i></button>
                </form>
              </td>
              <?php $traverse($node->children, $prefix.'___'); ?>
            </tr>
          <?php endforeach; ?>
        <?php }; ?>
        <?php $traverse($regions); ?>
      </tbody>
    </table>
  </div>

  {{-- $regions->links() --}}

@endsection