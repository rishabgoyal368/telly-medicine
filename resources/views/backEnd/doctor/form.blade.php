<?php

if (isset($user)) {
	$task = "Edit";
} else {
	$task = "Add";
}
?>
@extends('backEnd.layouts.master')
@section('title', ' Doctor')
@section('content')

<div class="page-content-wrapper ">
	<div class="page-content">
		<div class="tab-content">
			<h3 class="page-title">
				Doctor Management </h3>

			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-bars"></i>
						<a href="{{ url('admin/manage-doctor') }}">Doctor Management</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="{{ Request::fullUrl() }}">{{ $task }} Doctor</a>
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
							{{ $task }} Doctor
						</div>
					</div>

					<div class="portlet-body form">
						<!-- BEGIN FORM-->
						<form action="{{url('/admin/add-doctor')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
							<div class="form-body">

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
										<label class="col-md-3 control-label">Email :</label>
										<div class="col-md-6">
											<input type="email" name="email" class="form-control" placeholder="Enter Email" value="{{ isset($user->email)?$user->email:'' }}" required="required" />
										</div>
									</div>
								</div>

								@if (!@$user->password)
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label">Password :</label>
										<div class="col-md-6">
											<input type="password" name="password" class="form-control" placeholder="Enter Password" value="" required="required" />
										</div>
									</div>
								</div>
								@endif

								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label">Mobile Number :</label>
										<div class="col-md-6">
											<input type="number" name="mobile_number" class="form-control" placeholder="Enter Mobile Number" value="{{isset($user->mobile_number)?$user->mobile_number:''}}" required="required" />
										</div>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-3 control-label">Gender :</label>
									<div class="col-md-6">
										<select name="gender" class="form-control">
											<option value="male" @if(@$user->gender == "male") selected @endif >Male</option>
											<option value="female" @if(@$user->gender == "female") selected @endif >Female</option>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label">Location :</label>
										<div class="col-md-6">
											<input type="text" name="location" class="form-control" placeholder="Enter location" value="{{ isset($user->location)?$user->location:'' }}" required="required" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<label class="col-md-3 control-label">Speciality :</label>
										<div class="col-md-6">
											<input type="text" name="speciality" class="form-control" placeholder="Enter Speciality" value="{{ isset($user->speciality)?$user->speciality:'' }}" required="required" />
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


								<div class="form-group">
									<label class="col-md-3 control-label">Status :</label>
									<div class="col-md-6">
										<select name="status" class="form-control">
											<option value="Active" @if(@$user->status == "Active") selected @endif >Active</option>
											<option value="InActive" @if(@$user->status == "InActive") selected @endif>Inactive</option>
										</select>
									</div>
								</div>


								<?php
								// dd($user->profile_image);
								if (!empty($user->profile_image)) {
									$image = env('APP_URL') . 'uploads/doctor' . '/' . $user->profile_image;
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
										<input type="file" id="img_upload" name="profile_image">
									</div>
								</div>
							</div>



							<div class="form-actions top">
								<div class="row">
									<div class="col-md-offset-3 col-md-9">
										{{ csrf_field() }}
										<input type="hidden" name="id" value="{{ @$user['id'] }}" id="id">
										<button type="submit" class="btn green">Submit</button>
										<a href="{{ url('admin/user') }}"><button class="btn btn-default m-l-10" type="button">Cancel </button></a>
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