@extends('layouts.app')

@section('extra_css')
<style>
</style>
@endsection

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0">
            <li class="breadcrumb-item active"><span>Home</span>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="container-lg px-3">
        <h6>Welcome to Home Page</h6>
        {{-- <div class="spinner-red-blue spinner-red-blue-md"></div>
        <div class="stripes-red-blue stripes-red-blue-md"></div> --}}
    </div>
@endsection

@section('extra_js')
@endsection