{{--
    Usage: @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => 'Create Order'],
    ]])
--}}
<div class="col-sm-6">
    <ol class="breadcrumb float-sm-right" aria-label="breadcrumb">
        @foreach($crumbs as $crumb)
            @if(!$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</div>
