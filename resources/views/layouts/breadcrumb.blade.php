@if(isset($breadcrumb))
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>{{ $breadcrumb->title }}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            @foreach ($breadcrumb->list as $key => $value)
              @if ($key == count($breadcrumb->list) - 1)
                <li class="breadcrumb-item active">{{ $value }}</li>
              @else
                <li class="breadcrumb-item">{{ $value }}</li>
              @endif
            @endforeach
          </ol>
        </div>
      </div>
    </div>
</section>
@endif

<style>
.content-header h1 {
    color: #6f6f6e;
    font-weight: 600;
    font-size: 1.2rem;
}

.breadcrumb-item.active {
    color: #3498db;
    font-weight: 500;
}

.breadcrumb-item {
    color: #7f8c8d;
}
</style>