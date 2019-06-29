@extends('layouts.default_layout')
@section('title',"로그인")  
@section('meta-custom')
<meta name="robots" content="noindex, nofollow">
@stop

@section('content')
<div style="background-color: #34b599; padding: 20px;">
	<div class="container" style="padding-top: 2rem; padding-bottom: 1rem;">
		<h1>Account</h1>
		<p class="lead">로그인 을 하거나 계정관리 를 합니다.</p>
	</div>
</div>

<div class="container py-5">
	<div class="col-md-10 offset-md-1">
		<div class="card">
			<div class="card-body">
				<form class="form-horizontal {{ $errors->has('email') ? 'was-validated' : 'is-valid' }}" role="form" method="POST"
					action="{{ route('login') }}">
					{{ csrf_field() }}
					<h1>로그인</h1>
					<h5 class="text-muted pb-4"></h5>
					<div class="form-group row">
						<label for="email" class="col-form-label col-md-3 text-right">이메일</label>
						<div class="col-md-9">
							<input type="email" class="form-control" name="email" id="email"
								value="{{ old('email') }}" placeholder="이메일 또는 아이디 를 입력합니다" required autofocus>

							@if($errors->has('email')) 
							<span class="help-block"> <strong>{{$errors->first('email')}}</strong></span> 
							@endif
						</div>
					</div>

					<div class="form-group row">
						<label for="password" class="col-form-label col-md-3 text-right">암호</label>
						<div class="col-md-9">
							<input type="password" class="form-control"
								name="password" id="password" placeholder="비밀번호 를 입력합니다" required> 
							@if ($errors->has('password'))
							<span class="help-block"> <strong>{{ $errors->first('password') }}</strong></span> 
							@endif
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 offset-md-3">
							<div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                              <label class="custom-control-label" for="remember">기억하기</label>
                            </div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 offset-md-3">
							<button type="submit" class="btn btn-success">로그인</button>

							<?php if(false){ ?>
							<a class="btn btn-link" href="{{route('password.request')}}">
								Forgot Your Password? </a>
							 <?php } ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop