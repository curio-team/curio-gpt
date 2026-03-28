@php
    $errorCode = '404';
    $title = __('app.errors.page_not_found');
    $message = __('app.errors.not_found_message');
    $redirectUrl = route('home');
    $redirectLabel = __('app.errors.go_back_home');
@endphp
@include('errors.error')
