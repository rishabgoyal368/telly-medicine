<?php

if (isset($user)) {
	$task = "Edit";
} else {
	$task = "Add";
}
?>
@extends('backEnd.layouts.master')
@section('title', ' Resource')
@section('content')

<div class="page-content-wrapper ">
	<div class="page-content">
		<div class="tab-content">
			<h3 class="page-title">
				Resource Management </h3>

			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-bars"></i>
						<a href="{{ url('admin/manage-resource') }}">Resource Management</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="{{ Request::fullUrl() }}">{{ $task }} Resource</a>
					</li>
				</ul>
			</div>
			@if ($errors->any())
			@foreach ($errors->all() as $error)
			<div class="alert alert-danger alert-block">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>{{$error}}</strong>
			</div>
			@endforeach
			@endif
			<div class="tab-pane active" id="tab_0">
				<div class="portlet box blue-hoki">
					<div class="portlet-title">
						<div class="caption">
							{{ $task }} Resource
						</div>
					</div>

					<div class="portlet-body form">
						<!-- BEGIN FORM-->
						<form action="{{url('/admin/add-resource')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
							<div class="form-body">

								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label"> Title :</label>
										<div class="col-md-6">
											<input type="text" name="title" class="form-control" placeholder="Enter Title" value="{{ isset($user->title)?$user->title:'' }}" maxlength="255" required="required" />
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label"> Name :</label>
										<div class="col-md-6">
											<input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ isset($user->name)?$user->name:'' }}" maxlength="255" required="required" />
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label">Description :</label>
										<div class="col-md-6">
											<textarea name="description" id="description" cols="30" rows="10" class="form-control">{{ isset($user->description)?$user->description:'' }}</textarea>
										</div>
									</div>
								</div>

								<?php
								// dd($user->profile_image);
								if (!empty($user->image)) {
									$image = env('APP_URL') . 'uploads/resource' . '/' . $user->image;
								} else {
									$image = DefaultImgPath;
								}
								?>
								<div class="form-group">
									<label class="col-md-3 control-label">Image :</label>
									<div class="col-md-3 p-l-15 ">
										<img src="{{$image}}" width="80%" height="100%" id="old_image" alt="No image">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">New Image :</label>
									<div class="col-md-6">
										<input type="file" accept="image/png, image/jpeg, image/jpg" id="image" name="image">
									</div>
								</div>
							</div>



							<div class="form-actions top">
								<div class="row">
									<div class="col-md-offset-3 col-md-9">
										{{ csrf_field() }}
										<input type="hidden" name="id" value="{{ @$user['id'] }}" id="id">
										<button type="submit" class="btn green">Submit</button>
										<a href="{{ url('admin/manage-resource') }}"><button class="btn btn-default m-l-10" type="button">Cancel </button></a>
									</div>
								</div>
							</div>
						</form>
						<!-- END FORM-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">
	$(document).ready(function() {
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$('#old_image').attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}

		$('#img_upload').change(function() {

			var img_name = $(this).val();

			if (img_name != '' && img_name != null) {
				var img_arr = img_name.split('.');

				var ext = img_arr.pop();
				ext = ext.toLowerCase();
				// alert(ext); return false;

				if (ext == 'jpeg' || ext == 'jpg' || ext == 'png') {
					input = document.getElementById('img_upload');

					readURL(this);
				}
			} else {

				$(this).val('');
				alert('Please select an image of .jpeg, .jpg, .png file format.');
			}
		});
	});
</script>
@endsection