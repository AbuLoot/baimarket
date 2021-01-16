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
      <input type="tel" pattern="(\+?\d[- .]*){7,13}" class="form-control" name="phone" placeholder="Номер телефона*" value="{{ (old('phone')) ? old('phone') : $user->profile->phone }}" required>
    </div>
    <div class="form-group">
      <label>Регион</label>
      <select id="region_id" name="region_id" class="form-control">
        <option value=""></option>
        <?php $traverse = function ($nodes, $prefix = null) use (&$traverse, $user) { ?>
          <?php foreach ($nodes as $node) : ?>
            <option value="{{ $node->id }}" <?= ($node->id == $user->profile->region_id) ? 'selected' : ''; ?>>{{ PHP_EOL.$prefix.' '.$node->title }}</option>
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
      <label>Гос. номер*</label>
      <input type="text" class="form-control" name="gov_number" minlength="3" maxlength="30" placeholder="Гос. номер*" value="{{ (old('gov_number')) ? old('gov_number') : $user->privilege->gov_number }}" required>
    </div>
    <div class="form-group">
      <div><label>Тип карты</label></div>
      @foreach(trans('data.card_types') as $key => $card_type)
        <label>
          <input type="radio" name="card_type" value="{{ $key }}" @if($key == $user->privilege->card_type) checked="checked" @endif> {{ $card_type }}
        </label>
      @endforeach
    </div>
    <div class="form-group">
      <label>Штрих код карты</label>
      <input type="text" class="form-control" name="barcode" placeholder="Штрих код карты*" value="{{ (old('barcode')) ? old('barcode') : $user->privilege->barcode }}">
    </div>
    <div class="form-group">
      <label>Дата рождения</label>
      <input type="date" class="form-control" name="birthday" minlength="3" maxlength="30" placeholder="Дата рождения" value="{{ (old('birthday')) ? old('birthday') : $user->profile->birthday }}" >
    </div>
    <div class="form-group">
      <div><label>Пол</label></div>
      @foreach(trans('data.sex') as $key => $value)
        <label>
          <input type="radio" name="sex" @if($key == $user->profile->sex) checked="checked" @endif value="{{ $key }}"> {{ $value }}
        </label>
      @endforeach
    </div>
    <div class="form-group">
      <label for="about">О себе</label>
      <textarea class="form-control" id="about" name="about" rows="5">{{ (old('about')) ? old('about') : $user->profile->about }}</textarea>
    </div>

    <div class="form-group">
      <label for="roles_id">Роли:</label>
      <select class="form-control" name="roles_id[]" id="roles_id" multiple required>
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
      <label for="status">Статус карты</label>
      <label>
        @if ($user->privilege->status == 1)
          <input type="checkbox" id="status" name="privilege_status" checked> Активен
        @else
          <input type="checkbox" id="status" name="privilege_status"> Активен
        @endif
      </label>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Изменить</button>
    </div>
  </form>
@endsection
