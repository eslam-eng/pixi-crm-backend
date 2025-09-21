@extends('layouts.app')

@section('content')

<!-- container -->
<div class="main-container container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content"> <span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span> </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-body d-md-flex">
                    <div class="">
                        <span class="profile-image pos-relative">
                            <img class="br-5" alt="" src="{{asset(Auth::user()->profileImage??'assets/images/users/default.png') }}">
                            <span class="bg-success text-white wd-1 ht-1 rounded-pill {{ Auth::user()->status ? "
                                profile-online": "profile-offline" }}"></span>
                        </span>
                    </div>
                    <div class="my-md-auto mt-4 prof-details">
                        <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">{{ Auth::user()->name }}</h4>
                        <p class="tx-13 text-muted ms-md-4 ms-0 mb-2 pb-2 ">
                            <span class="me-3"><i class="far fa-address-card me-2"></i>{{ Auth::user()->role }}</span>
                        </p>
                        <p class="text-muted ms-md-4 ms-0 mb-2">
                            <span><i class="fa fa-phone me-2"></i></span>
                            <span class="font-weight-semibold me-2">Phone:</span><span>{{ Auth::user()->phone }}</span>
                        </p>
                        <p class="text-muted ms-md-4 ms-0 mb-2">
                            <span><i class="fa fa-envelope me-2"></i></span>
                            <span class="font-weight-semibold me-2">Email:</span><span>{{ Auth::user()->email }}</span>
                        </p>
                        <p class="text-muted ms-md-4 ms-0 mb-2">
                            <span><i class="fa fa-home me-2"></i></span>
                            <span class="font-weight-semibold me-2">Address</span>
                            <span>{{ Auth::user()->address }}</span>
                        </p>
                    </div>
                </div>
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0	">
                            <a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#edit">Edit Profile</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">
                    <div class="main-content-body tab-pane border-top-0 active" id="edit">
                        <div class="card">
                            <div class="card-body border-0">
                                @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <form class="form-horizontal" method="post"
                                    action="{{ route('profile.update', Auth::user()->id) }}">
                                    @csrf
                                    @method('put')
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Name</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ Auth::user()->name }}">
                                                @error('name')
                                                <div class="text-danger"> {{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Email</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="email" class="form-control"
                                                    value="{{ Auth::user()->email }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">phone</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="numeric" name="phone" class="form-control"
                                                    value="{{ Auth::user()->phone }}">
                                                @error('phone')
                                                <div class="text-danger"> {{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row row-sm">
                                            <div class="col-md-3">
                                                <label class="form-label">Address</label>
                                            </div>
                                            <div class="col-md-9">
                                                <textarea class="form-control" name="address" rows="2"
                                                    placeholder="Address">{{ Auth::user()->address }}</textarea>
                                                @error('address')
                                                <div class="text-danger"> {{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div>
                                            <button type="submit" class="btn btn-primary"><i
                                                    class="fa fa-save pe-2"></i>@lang('app.save')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
</div>
<!-- Container closed -->
@endsection