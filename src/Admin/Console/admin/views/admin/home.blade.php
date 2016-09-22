@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
	{!! Breadcrumbs::render('admin') !!}
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Home</h3>
    </div>
    <div class="box-body">
        Welcome back {{ auth()->guard('admin')->user()->name }}!
    </div>
</div>


@endsection
