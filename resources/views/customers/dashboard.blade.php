@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            @include('customers.kalender')

        </div>
    </div>
@endsection
