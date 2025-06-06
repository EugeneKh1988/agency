@extends('email.main')

@section('title', 'Подтверждение email')

@section('style')
<style type="text/css">
	.title {
		text-align: center;
		font-size: 2em;
		background-color: #1976d2;
		padding: 10px 0;
		text-transform: uppercase;
		margin: 0;
		margin-bottom: 10px;
		color: #fff;
	}

	.content {
		background-color: #BBDEFB;
		padding: 10px;
		font-size: 1.2em;
		color: rgba(0,0,0,0.8);
	}

	.content .code {
		background-color: #4F9DE9;
		font-style: normal;
		color: #fff;
		display: inline-block;
		padding: 10px;
		}
	
	.content a {
		text-wrap: wrap;
		overflow-wrap: break-word;
	}

	.container {
		width: 100%;
		max-width: 100%;
	}

	.footer {
		background-color: #1976d2;
		padding: 10px;
		color: #fff;
		font-size: 1.2em;

	}
</style>
@endsection

@section('content')
<p class="title">Agency.com</p>
<div class="content">
	Please click on the following link in order to verify as subscriber - 
	<a href="{{ $link }}">{{ $link }}</a>
	<p>If you received this email by mistake, simply delete it. You will not be subscribed if you do not  click the confirmation link above.</p>
</div>
<p class="footer">Best regards, the team of Agency.com</p>
@endsection