@extends('layouts.app')

    @section('styles')

    @endsection

    @section('content')

					<!-- breadcrumb -->
					<div class="breadcrumb-header justify-content-between">
						<div class="left-content">
						<span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span>
						</div>
						<div class="justify-content-center mt-2">
							<ol class="breadcrumb">
								<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
							</ol>
						</div>
					</div>
					<!-- /breadcrumb -->

					<!-- row -->
					<div class="row">
						<div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
									<div class="card">
										<div class="card-body">
											<div class="row">
												<div class="col-xl-9 col-lg-7 col-md-6 col-sm-12">
													<div class="text-justified align-items-center">
														<h3 class="text-dark font-weight-semibold mb-2 mt-0">Hi,{{auth()->user()->name}} </h3>
														<p class="text-dark tx-14 mb-3 lh-3">system upgrade working.... </p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
{{--								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">--}}
{{--									<div class="card sales-card">--}}
{{--										<div class="row">--}}
{{--											<div class="col-8">--}}
{{--												<div class="ps-4 pt-4 pe-3 pb-4">--}}
{{--													<div class="">--}}
{{--														<h6 class="mb-2 tx-12 ">Today Orders</h6>--}}
{{--													</div>--}}
{{--													<div class="pb-0 mt-0">--}}
{{--														<div class="d-flex">--}}
{{--															<h4 class="tx-20 font-weight-semibold mb-2">5,472</h4>--}}
{{--														</div>--}}
{{--														<p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>--}}
{{--															<span class="text-success font-weight-semibold"> +427</span>--}}
{{--														</p>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											<div class="col-4">--}}
{{--												<div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">--}}
{{--													<i class="fe fe-shopping-bag tx-16 text-primary"></i>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">--}}
{{--									<div class="card sales-card">--}}
{{--										<div class="row">--}}
{{--											<div class="col-8">--}}
{{--												<div class="ps-4 pt-4 pe-3 pb-4">--}}
{{--													<div class="">--}}
{{--														<h6 class="mb-2 tx-12">Today Earnings</h6>--}}
{{--													</div>--}}
{{--													<div class="pb-0 mt-0">--}}
{{--														<div class="d-flex">--}}
{{--															<h4 class="tx-20 font-weight-semibold mb-2">$47,589</h4>--}}
{{--														</div>--}}
{{--														<p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>--}}
{{--															<span class="font-weight-semibold text-danger"> -453</span>--}}
{{--														</p>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											<div class="col-4">--}}
{{--												<div class="circle-icon bg-info-transparent text-center align-self-center overflow-hidden">--}}
{{--													<i class="fe fe-dollar-sign tx-16 text-info"></i>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">--}}
{{--									<div class="card sales-card">--}}
{{--										<div class="row">--}}
{{--											<div class="col-8">--}}
{{--												<div class="ps-4 pt-4 pe-3 pb-4">--}}
{{--													<div class="">--}}
{{--														<h6 class="mb-2 tx-12">Profit Gain</h6>--}}
{{--													</div>--}}
{{--													<div class="pb-0 mt-0">--}}
{{--														<div class="d-flex">--}}
{{--															<h4 class="tx-20 font-weight-semibold mb-2">$8,943</h4>--}}
{{--														</div>--}}
{{--														<p class="mb-0 tx-12 text-muted">Last week<i class="fa fa-caret-up mx-2 text-success"></i>--}}
{{--															<span class=" text-success font-weight-semibold"> +788</span>--}}
{{--														</p>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											<div class="col-4">--}}
{{--												<div class="circle-icon bg-secondary-transparent text-center align-self-center overflow-hidden">--}}
{{--													<i class="fe fe-external-link tx-16 text-secondary"></i>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--								<div class="col-xl-6 col-lg-12 col-md-12 col-xs-12">--}}
{{--									<div class="card sales-card">--}}
{{--										<div class="row">--}}
{{--											<div class="col-8">--}}
{{--												<div class="ps-4 pt-4 pe-3 pb-4">--}}
{{--													<div class="">--}}
{{--														<h6 class="mb-2 tx-12">Total Earnings</h6>--}}
{{--													</div>--}}
{{--													<div class="pb-0 mt-0">--}}
{{--														<div class="d-flex">--}}
{{--															<h4 class="tx-22 font-weight-semibold mb-2">$57.12M</h4>--}}
{{--														</div>--}}
{{--														<p class="mb-0 tx-12  text-muted">Last week<i class="fa fa-caret-down mx-2 text-danger"></i>--}}
{{--															<span class="text-danger font-weight-semibold"> -693</span>--}}
{{--														</p>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											<div class="col-4">--}}
{{--												<div class="circle-icon bg-warning-transparent text-center align-self-center overflow-hidden">--}}
{{--													<i class="fe fe-credit-card tx-16 text-warning"></i>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--								<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">--}}
{{--									<div class="card">--}}
{{--										<div class="card-header pb-1">--}}
{{--											<h3 class="card-title mb-2">Browser Usage</h3>--}}
{{--										</div>--}}
{{--										<div class="card-body p-0">--}}
{{--											<div class="browser-stats">--}}
{{--												<div class="d-flex align-items-center item  border-bottom my-2">--}}
{{--													<div class="d-flex">--}}
{{--														<img src="{{asset('assets/img/svgicons/chrome.svg')}}" alt="img" class="ht-30 wd-30 me-2">--}}
{{--														<div class="">--}}
{{--															<h6 class="">Chrome</h6>--}}
{{--															<span class="text-muted tx-12">Google, Inc.</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--													<div class="ms-auto my-auto">--}}
{{--														<div class="d-flex">--}}
{{--															<span class="me-4 mt-1 font-weight-semibold tx-16">35,502</span>--}}
{{--															<span class="text-success fs-13 my-auto"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>12.75%</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--												<div class="d-flex align-items-center item  border-bottom my-2">--}}
{{--													<div class="d-flex">--}}
{{--														<img src="{{asset('assets/img/svgicons/edge.svg')}}" alt="img" class="ht-30 wd-30 me-2">--}}
{{--														<div class="">--}}
{{--															<h6 class="">Edge</h6>--}}
{{--															<span class="text-muted tx-12">Microsoft Corporation, Inc.</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--													<div class="ms-auto my-auto">--}}
{{--														<div class="d-flex">--}}
{{--															<span class="me-4 mt-1 font-weight-semibold tx-16">25,364</span>--}}
{{--															<span class="text-success"><i class="fe fe-trending-down text-danger me-2 ms-1 my-auto"></i>24.37%</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--												<div class="d-flex align-items-center item  border-bottom my-2">--}}
{{--													<div class="d-flex">--}}
{{--														<img src="{{asset('assets/img/svgicons/firefox.svg')}}" alt="img" class="ht-30 wd-30 me-2">--}}
{{--														<div class="">--}}
{{--															<h6 class="">Firefox</h6>--}}
{{--															<span class="text-muted tx-12">Mozilla Foundation, Inc.</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--													<div class="ms-auto my-auto">--}}
{{--														<div class="d-flex">--}}
{{--															<span class="me-4 mt-1 font-weight-semibold tx-16">14,635</span>--}}
{{--															<span class="text-success"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>15,63%</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--												<div class="d-flex align-items-center item  border-bottom my-2">--}}
{{--													<div class="d-flex">--}}
{{--														<img src="{{asset('assets/img/svgicons/safari.svg')}}" alt="img" class="ht-30 wd-30 me-2">--}}
{{--														<div class="">--}}
{{--															<h6 class="">Safari</h6>--}}
{{--															<span class="text-muted tx-12">Apple Corporation, Inc.</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--													<div class="ms-auto my-auto">--}}
{{--														<div class="d-flex">--}}
{{--															<span class="me-4 mt-1 font-weight-semibold tx-16">35,657</span>--}}
{{--															<span class="text-danger"><i class="fe fe-trending-up text-success me-2 ms-1 my-auto"></i>12.54%</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--												<div class="d-flex align-items-center item my-2">--}}
{{--													<div class="d-flex">--}}
{{--														<img src="{{asset('assets/img/svgicons/opera.svg')}}" alt="img" class="ht-30 wd-30 me-2">--}}
{{--														<div class="">--}}
{{--															<h6 class="">Opera</h6>--}}
{{--															<span class="text-muted tx-12">Opera, Inc.</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--													<div class="ms-auto my-auto">--}}
{{--														<div class="d-flex">--}}
{{--															<span class="me-4 mt-1 font-weight-semibold tx-16">12,563</span>--}}
{{--															<span class="text-danger"><i class="fe fe-trending-down text-danger me-2 ms-1 my-auto"></i>15.12%</span>--}}
{{--														</div>--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--								</div>--}}
							</div>
						</div>
					</div>
					<!-- row closed -->

    @endsection

    @section('scripts')

    @endsection
