@extends('layouts.app')

@section('extra_css')
<style>
</style>
@endsection

@section('content')
    <div class="full-height-column-wrapper">
        <div class="card-body d-flex justify-content-center align-items-center">
            <div class="text-center">
                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                <div class="d-block fs-7 mt-2">{{ $message }}</div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
@endsection