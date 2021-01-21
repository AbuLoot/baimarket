
  @if (session('info'))
    <div class="alert alert-info">
      {{ session('info') }}
      <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('warning'))
    <div class="alert alert-warning">
      {{ session('warning') }}
      <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

	@if (session('status'))
	  <div class="alert alert-success">
	    {{ session('status') }}
	    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
	  </div>
	@endif

  @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif