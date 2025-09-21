<!-- main-header -->
<div class="main-header side-header sticky nav nav-item">
    <div class=" main-container container-fluid">
        <div class="main-header-left ">
            <div>
                <div class="responsive-logo">
                    <a href="{{url('/')}}" class="header-logo">
                        <img style="max-height: 40px !important" src="{{asset('assets/images/brand/logo.png')}}"
                            class="mobile-logo wd-25p" alt="logo">
                    </a>
                </div>
                <div class="app-sidebar__toggle" data-bs-toggle="sidebar">
                    <a class="open-toggle" href="javascript:void(0);"><i class="header-icon fe fe-align-left"></i></a>
                    <a class="close-toggle" href="javascript:void(0);"><i class="header-icon fe fe-x"></i></a>
                </div>
                <div class="logo-horizontal">
                    <a href="{{url('/')}}" class="header-logo">
                        <img style="max-height: 40px !important" src="{{asset('assets/images/brand/logo.png')}}"
                            class="mobile-logo wd-25p" alt="logo">
                    </a>
                </div>
            </div>
            <div>
                <div class="mt-2 p-2 mx-auto">
                    <h4>@lang("app.hi"), {{auth()->user()->name}}</h4>
                </div>
            </div>
        </div>
        <div class="main-header-right">
            <button class="navbar-toggler navresponsive-toggler d-md-none ms-auto" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon fe fe-more-vertical "></span>
            </button>
            <div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0">
                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                    <ul class="nav nav-item header-icons navbar-nav-right ms-auto">

                        <li class="dropdown main-profile-menu nav nav-item nav-link ps-lg-2 ">
                            <div class="w-full">
                                <a class="new nav-item d-flex" href="" data-bs-toggle="dropdown"
                                    aria-expanded="true"><svg class="header-icon-svgs"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm7.931 9h-2.764a14.67 14.67 0 0 0-1.792-6.243A8.013 8.013 0 0 1 19.931 11zM12.53 4.027c1.035 1.364 2.427 3.78 2.627 6.973H9.03c.139-2.596.994-5.028 2.451-6.974.172-.01.344-.026.519-.026.179 0 .354.016.53.027zm-3.842.7C7.704 6.618 7.136 8.762 7.03 11H4.069a8.013 8.013 0 0 1 4.619-6.273zM4.069 13h2.974c.136 2.379.665 4.478 1.556 6.23A8.01 8.01 0 0 1 4.069 13zm7.381 6.973C10.049 18.275 9.222 15.896 9.041 13h6.113c-.208 2.773-1.117 5.196-2.603 6.972-.182.012-.364.028-.551.028-.186 0-.367-.016-.55-.027zm4.011-.772c.955-1.794 1.538-3.901 1.691-6.201h2.778a8.005 8.005 0 0 1-4.469 6.201z" />
                                    </svg></a>
                                <div class="dropdown-menu" data-bs-popper="none">
                                    <a class="dropdown-item" href="#"><span class="country-selector"><img alt=""
                                                src="http://crm.test/assets/img/flags/eg.svg"
                                                class="me-3 language"></span>AR</a>
                                    <a class="dropdown-item" href="#"><span class="country-selector"><img alt=""
                                                src="http://crm.test/assets/img/flags/us_flag.jpg"
                                                class="me-3 language"></span>En</a>
                                </div>
                            </div>
                        </li>

                        <li
                            class="dropdown nav-item main-header-notification d-flex border-end border-start me-3 pe-3 ms-3 ps-3">

                            <a class="new nav-link" data-bs-toggle="dropdown" href="javascript:void(0);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" width="24" height="24"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M19 13.586V10c0-3.217-2.185-5.927-5.145-6.742C13.562 2.52 12.846 2 12 2s-1.562.52-1.855 1.258C7.185 4.074 5 6.783 5 10v3.586l-1.707 1.707A.996.996 0 0 0 3 16v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L19 13.586zM19 17H5v-.586l1.707-1.707A.996.996 0 0 0 7 14v-4c0-2.757 2.243-5 5-5s5 2.243 5 5v4c0 .266.105.52.293.707L19 16.414V17zm-7 5a2.98 2.98 0 0 0 2.818-2H9.182A2.98 2.98 0 0 0 12 22z">
                                    </path>
                                </svg><span class=" pulse"></span>
                            </a>


                            <div class="dropdown-menu">
                                <div class="menu-header-content text-start border-bottom">
                                    <div class="d-flex">
                                        <h6 class="dropdown-title mb-1 tx-15 font-weight-semibold">Notifications</h6>
                                        <span class="badge badge-pill badge-warning ms-auto my-auto float-end">Mark All
                                            Read</span>
                                    </div>
                                    <p class="dropdown-title-text subtext mb-0 op-6 pb-0 tx-12 ">You have 4 unread
                                        Notifications</p>
                                </div>
                                <div class="main-notification-list Notification-scroll ps">
                                    <a class="d-flex p-3 border-bottom" href="http://pure-nowa.test/mail">
                                        <div class="notifyimg bg-pink">
                                            <i class="far fa-folder-open text-white"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">New files available</h5>
                                            <div class="notification-subtext">10 hour ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>

                                    <a class="d-flex p-3 border-bottom" href="http://pure-nowa.test/mail">
                                        <div class="">
                                            <i class="far fa-check-square text-white notifyimg bg-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">Project has been approved</h5>
                                            <span class="notification-subtext">4 hour ago</span>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                    </div>
                                    <div class="ps__rail-y" style="top: 0px; right: 0px;">
                                        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                                    </div>
                                </div>
                                <div class="dropdown-footer">
                                    <a class="btn btn-primary btn-sm btn-block" href="http://pure-nowa.test/mail">VIEW
                                        ALL</a>
                                </div>
                            </div>
                        </li>

                        <li class="dropdown main-profile-menu nav nav-item nav-link ps-lg-2">
                            <a class="new nav-link profile-user d-flex main-img-user" href=""
                                data-bs-toggle="dropdown"><img alt=""
                                    src="{{ asset(Auth::user()->profileImage??'assets/images/users/default.png') }}"
                                    class=""></a>
                            <div class="dropdown-menu">
                                <div class="menu-header-content p-3 border-bottom">
                                    <div class="d-flex wd-100p">
                                        <div class="main-img-user"><img alt=""
                                                src="{{ asset(Auth::user()->profileImage??'assets/images/users/default.png') }}"
                                                class=""></div>
                                        <div class="ms-3 my-auto">
                                            <h6 class="tx-15 font-weight-semibold mb-0">{{auth()->user()->name}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="{{route('profile.index')}}"><i
                                        class="far fa-user-circle"></i>Profile</a>
                                <a class="dropdown-item" href="{{route('logout')}}"><i
                                        class="far fa-arrow-alt-circle-left"></i> Sign Out</a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!--place for switcher icon-->
            </div>
        </div>
    </div>
</div>
<!-- /main-header -->