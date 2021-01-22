@extends('joystick-admin.layout')

@section('content')
  <h2 class="page-header">Редактирование</h2>

  @include('joystick-admin.partials.alerts')

  <p class="text-right">
    <a href="/{{ $lang }}/admin/users" class="btn btn-primary btn-sm">Назад</a>
  </p>
  <form action="{{ route('users.update', [$lang, $user->id]) }}" method="post" enctype="multipart/form-data">
    <input name="_method" type="hidden" value="PUT">
    {!! csrf_field() !!}

    <div class="row">
      <div class="col-6 col-md-6">
        <div class="form-group">
          <label>Имя</label>
          <input type="text" class="form-control" minlength="2" maxlength="40" name="name" placeholder="Имя*" value="{{ (old('name')) ? old('name') : $user->name }}" required>
        </div>
      </div>
      <div class="col-6 col-md-6">
        <div class="form-group">
          <label>Фамилия</label>
          <input type="text" class="form-control" minlength="2" maxlength="60" name="surname" placeholder="Фамилия*" value="{{ (old('surname')) ? old('surname') : $user->surname }}" required>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" name="email" id="email" minlength="8" maxlength="60" value="{{ $user->email }}" required>
    </div>
    <div class="form-group">
      <label>Номер телефона</label>
      <input type="tel" pattern="(\+?\d[- .]*){7,13}" class="form-control" name="phone" placeholder="Номер телефона*" value="{{ (old('phone')) ? old('phone') : '' }}">
    </div>
    <div class="form-group">
      <label>Регион</label>
      <select id="region_id" name="region_id" class="form-control">
        <option value=""></option>
        <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $user) { ?>
          <?php foreach ($nodes as $node) : ?>
            <option value="{{ $node->id }}">{{ PHP_EOL.$prefix.' '.$node->title }}</option>
            <?php $traverse($node->children, $prefix.'___'); ?>s
          <?php endforeach; ?>
        <?php }; ?>
        <?php $traverse($regions); ?>
      </select>
    </div>
    <div class="form-group">
      <label>Баланс</label>
      <input type="text" class="form-control" name="balance" maxlength="30" placeholder="Баланс*" value="{{ (old('balance')) ? old('balance') : $user->balance }}">
    </div>
    <div class="form-group">
      <label>Дата рождения</label>
      <input type="date" class="form-control" name="birthday" minlength="3" maxlength="30" placeholder="Дата рождения" value="{{ (old('birthday')) ? old('birthday') : '' }}" >
    </div>
    <div class="form-group">
      <div><label>Пол</label></div>
      @foreach(trans('data.sex') as $key => $value)
        <label>
          <input type="radio" name="sex" value="{{ $key }}"> {{ $value }}
        </label>
      @endforeach
    </div>
    <div class="form-group">
      <label for="about">О себе</label>
      <textarea class="form-control" id="about" name="about" rows="5">{{ (old('about')) ? old('about') : '' }}</textarea>
    </div>

    <div class="form-group">
      <label for="roles_id">Роли:</label>
      <select class="form-control" name="roles_id[]" id="roles_id" multiple>
        <option value=""></option>
        @foreach($roles as $role)
          @if ($user->roles->contains($role->id)))
            <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
          @else
            <option value="{{ $role->id }}">{{ $role->name }}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Изменить</button>
    </div>
  </form>
@endsection
