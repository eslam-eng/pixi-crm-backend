@extends('central.app')

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
									<h3 class="text-dark font-weight-semibold mb-2 mt-0">Hi,{{auth()->user()->name}}
									</h3>
									<h3 class="text-dark font-weight-semibold mb-2 mt-0">
										you owner of {{auth()->user()->tenant->name}}
									</h3>
									<p class="text-dark tx-14 mb-3 lh-3">system upgrade working.... </p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- row closed -->

@endsection

@section('scripts')

@endsection