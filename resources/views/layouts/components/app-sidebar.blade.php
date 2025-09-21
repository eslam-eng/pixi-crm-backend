<!-- main-sidebar -->
<div class="sticky">
    <aside class="app-sidebar">
        <div class="main-sidebar-header active">
            <a class="header-logo active" href="{{url('/')}}">
                {{-- <img style="max-height: 40px !important" src="{{asset('assets/images/brand/logo.png')}}"
                    class="main-logo" alt="logo"> --}}
                <img src="{{asset('assets/images/brand/logo.png')}}" class="main-logo  desktop-logo" alt="logo">
                <img src="{{asset('assets/images/brand/logo-white.png')}}" class="main-logo  desktop-dark" alt="logo">
                <img src="{{asset('assets/images/brand/favicon.png')}}" class="main-logo  mobile-logo" alt="logo">
                <img src="{{asset('assets/images/brand/favicon-white.png')}}" class="main-logo  mobile-dark" alt="logo">
            </a>
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">

                {{-- menu --}}
                <li class="side-item side-item-category">@lang('app.menu')</li>
                <!-- Contact Menu -->

                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('contacts.index')) }}"
                        href="{{ route('contacts.index') }}"><i
                            class="fa-kit fa-contact-svg pe-2"></i>@lang('app.contacts')</a>
                </li>

                <!-- lead Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('leads.index')) }}"
                        href="{{ route('leads.index') }}"><i
                            class="fa-kit fa-opportunity-svg  pe-2"></i>@lang('app.leads')</a>
                </li>

                <li class="slide" style="padding: 7px 15px">
                    <span class="side-menu__item disabled" style="color: #c0c0c0;">@lang('app.settings')</span>
                </li>

                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('industries.index')) }}"
                        href="{{ route('industries.index') }}"><i
                            class="fa-thin fa-suitcase  pe-2"></i>@lang('app.industries')</a>
                </li>

                <!-- Reason Menu -->

                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('reasons.index')) }}"
                        href="{{ route('reasons.index') }}"><i
                            class="fa-kit fa-reason-svg pe-2"></i></i>@lang('app.reasons')</a>
                </li>

                <!-- source Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('resources.index')) }}"
                        href="{{ route('resources.index') }}"><i
                            class="fa-kit fa-source-svg pe-2"></i></i>@lang('app.sources')</a>
                </li>

                <!-- Service Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('services.index')) }}"
                        href="{{ route('services.index') }}"><i
                            class="fa-kit fa-service-svg pe-2"></i>@lang('app.services')</a>
                </li>


                <!-- Custom Fields Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('custom-fields.index')) }}"
                        href="{{ route('custom-fields.index') }}"><i
                            class="fa-thin fa-list-timeline pe-2"></i>@lang('app.customFields')</a>
                </li>

                <!-- Role Permissions Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('role-permissions.index')) }}"
                        href="{{ route('role-permissions.index') }}"><i
                            class="fa-thin fa-shield-keyhole pe-2"></i>@lang('app.role-permissions')</a>
                </li>

                <!-- Pipeline Menu -->
                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('piplines.index')) }}"
                        href="{{ route('piplines.index') }}"><i class="fa-thin fa-diagram-project pe-2"></i>
                        @lang('app.piplines')</a>
                </li>

                <li class="slide" style="padding: 7px 15px">
                    <a class="slide-item" data-is_active="{{ request()->fullUrlIs(route('locations.index')) }}"
                        href="{{ route('locations.index') }}"><i class="fa-sharp fa-thin fa-location-dot pe-2"></i>
                        @lang('app.locations')</a>
                </li>
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </aside>
</div>
<!-- main-sidebar -->
