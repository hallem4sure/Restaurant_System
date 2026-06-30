@extends('adminlte::page')

@section('title')
    {{ setting('restaurant.name', config('app.name')) }} - @yield('page_title', 'Dashboard')
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
        </div>
        @hasSection('breadcrumbs')
            @yield('breadcrumbs')
        @endif
    </div>
@stop

@section('content')
    @include('partials.alerts')
    @yield('main_content')
@stop

@section('css')
    @yield('custom_css')
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global SweetAlert2 delete/destructive confirmation
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const message = el.getAttribute('data-confirm') || 'Are you sure? This action cannot be undone.';
                const title = el.getAttribute('data-confirm-title') || 'Confirm Action';
                const icon = el.getAttribute('data-confirm-icon') || 'warning';
                const confirmText = el.getAttribute('data-confirm-btn') || 'Yes, proceed';
                const form = el.closest('form');

                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (form) {
                            form.submit();
                        } else if (el.tagName === 'A') {
                            window.location.href = el.href;
                        }
                    }
                });
            });
        });

        // Loading state on form submit buttons
        document.querySelectorAll('form[data-loading]').forEach(function (form) {
            form.addEventListener('submit', function () {
                const btn = form.querySelector('[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Please wait...';
                    // Restore after 10s as fallback
                    setTimeout(() => { btn.disabled = false; btn.innerHTML = original; }, 10000);
                }
            });
        });
    });
</script>
@yield('custom_js')
@stop
