@if (session('messages'))
    <div class="bg-green-100 rounded-lg p-4 mb-4 text-sm text-green-700" role="alert">
      {{ session('messages') }}
    </div>
@endif
@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="bg-red-100 rounded-lg p-4 mb-4 text-sm text-red-700" role="alert">
      {{ $error }}
    </div>
    @endforeach
@endif