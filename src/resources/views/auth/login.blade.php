@extends('layouts.app')

@section('content')
    <login-component csrf_token="{{ csrf_token() }}" form_action="{{ route('login') }}"></login-component>
@endsection
