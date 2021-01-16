@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Пользователи</h2>

  @include('joystick-admin.partials.alerts')

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr class="active">
          <td>№</td>
          <td>Имя</td>
          <td>Email</td>
          <td>Мои компании</td>
          <td>Номер телефона</td>
          <td>Город</td>
          <td>Гос. номер</td>
          <td>Тип карты</td>
          <td>Роль</td>
          <td class="text-right">Функции</td>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        @foreach ($users as $user)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
              @if($user->companies->isNotEmpty())
                @foreach($user->companies as $company)
                  {{ $company->title }}<br>
                @endforeach
              @endif
            </td>
            <td>{{ ($user->profile) ? $user->profile->phone : '' }}</td>
            <td>
              <?php
                $region = \App\Region::find(($user->profile) ? $user->profile->region_id : 0);
                if ($region) echo $region->title;
              ?>
            </td>
            <td>{{ ($user->privilege) ? $user->privilege->gov_number : '' }}</td>
            <td>{{ ($user->privilege) ? $user->privilege->card_type : '' }}</td>
            <td>
              @foreach ($user->roles as $role)
                {{ $role->name }}<br>
              @endforeach
            </td>
            <td class="text-right text-nowrap">
              <a class="btn btn-link btn-xs" href="{{ route('users.edit', [$lang, $user->id]) }}" title="Редактировать"><i class="material-icons md-18">mode_edit</i></a>
              <form method="POST" action="{{ route('users.destroy', [$lang, $user->id]) }}" accept-charset="UTF-8" class="btn-delete">
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
  {{ $users->links() }}

@endsection
