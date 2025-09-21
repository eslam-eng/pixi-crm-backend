@extends('central.custom-app')

@section('styles')

@endsection

@section('class')
<div style="background: #171f4e">
    @endsection

    @section('content')

    <div class="page-single">
        <div class="container">
            <div class="row">
                <div
                    class="col-xl-5 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main py-45 justify-content-center mx-auto">
                    <div class="card-sigin mt-5 mt-md-0">
                        <!-- Demo content-->
                        <div class="main-card-signin d-md-flex">
                            <div class="wd-100p">
                                <div class="d-flex mb-4"><a href="http://pure-nowa.test/index"><img
                                            src="http://pure-nowa.test/assets/img/brand/favicon.png"
                                            class="sign-favicon ht-40" alt="logo"></a></div>
                                <div class="">
                                    <div class="main-signup-header">
                                        <h2 class="text-dark">Get Started</h2>
                                        <h6 class="font-weight-normal mb-4">It's free to signup and only takes a minute.
                                        </h6>
                                        <form action="{{route('signup')}}" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                                            <div class="form-group">
                                                <label>@lang('app.name')</label>
                                                <input class="form-control" name="name" placeholder="@lang('app.name')"
                                                    type="text">
                                                @error('name')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('app.email')</label>
                                                <input class="form-control" name="email"
                                                    placeholder="@lang('app.email')" type="text">
                                                @error('email')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('app.tenant')</label>
                                                <input class="form-control" name="tenant"
                                                    placeholder="@lang('app.tenant')" type="text">
                                                @error('tenant')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('app.password')</label> <input name="password"
                                                    class="form-control" placeholder="********" type="password">

                                                @error('password')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('app.activation_code')</label>
                                                <input class="form-control" name="activation_code"
                                                    placeholder="@lang('app.activation_code')" type="text">
                                                @error('activation_code')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('app.image')</label>
                                                <input class="form-control" name="image" type="file" accept="image/*">
                                                @error('image')
                                                <div class="text-danger fw-bold">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <button type="submit"
                                                class="btn btn-primary btn-block">@lang('app.sign_up')</button>
                                        </form>
                                        <div class="main-signup-footer mt-3 text-center">
                                            <p>Already have an account? <a href="{{route('login')}}">Sign In</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection