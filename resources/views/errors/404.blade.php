@php
$errorCode = '404';
$title = __('Page not found');
$message = __('The page you\'re looking for has moved or doesn\'t exist.');
$redirectUrl = route('home');
$redirectLabel = __('Go back home');
@endphp
@include('errors.error')