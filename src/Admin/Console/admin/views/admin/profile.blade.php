@extends('admin.layouts.app')

@section('title', 'Profile Page')

@section('breadcrumb')
	{!! Breadcrumbs::render('profile') !!}
@endsection

@section('content')
<?php $admin = auth()->guard('admin')->user(); ?>
<div class="row">
	<div class="col-md-3">
	    <!-- Profile Image -->
	    <div class="box box-primary">
	        <div class="box-body box-profile">
	            <img class="profile-user-img img-responsive img-circle" src="{{ $admin->image or asset('admin/img/user2-160x160.jpg') }}" alt="User profile picture">
	            <h3 class="profile-username text-center">{{ $admin->name }}</h3>
	            
	            <a href="{{ route('admin.logout') }}" class="btn btn-primary btn-block"><b>Logout</b></a>
	        </div>
	        <!-- /.box-body -->
	    </div>
	</div>
	<div class="col-sm-9">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Profile Information</h3>
			</div>
			{!! Form::model($admin, ['url' => route('admin.profile'), 'method' => 'POST']) !!}
	        <div class="box-body box-profile">
	        	@include('admin.partials._errors')
				<div class="form-group">
					{!! Form::label('name', 'Name') !!}
					{!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('email', 'Email Address') !!}
					{!! Form::text('email', null, ['class' => 'form-control', 'id' => 'email']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('password', 'Password') !!}
					{!! Form::input('password', 'password', null, ['class' => 'form-control', 'id' => 'password']) !!}
				</div>
	        </div>
			<div class="box-footer">
				{!! Form::submit('Update Profile Information', ['class' => 'btn btn-primary']) !!}
			</div>
			{!! Form::close() !!}
	    </div>
	</div>
</div>
@endsection
