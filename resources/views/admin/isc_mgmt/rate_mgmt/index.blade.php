@extends('layouts.admin_layout') 
@section('title',"실수령액 계산기 관리 페이지")
@section('title-layout',"실수령액 계산기 용어 사전")
@section('content')
<script>
$(document).ready(function(){
});
</script>
<style>
.font-monospace{
	font-family: "Courier New", Courier, monospace;
}
</style>
<div class="container-fluid pt-4 pb-5">
	<div class="row">
		<div class="col order-md-2">
			<h2 class="pt-3">목록</h2>
			<table class="table table-striped table-hover table-dark table-sm">
				<thead class="thead-inverse">
					<tr>
						<th>해당년월</th>
						<th>국민연금</th>
						<th>건강보험</th>
						<th>요양보험</th>
						<th>고용보험</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($masterRecords as $item)
					<tr>
						<td class=""><a href="{{ route($ROUTE_ID.'.edit',$item->id) }}" class="text-light text-decoration-none">{{ $item->yearmonth }}</a></td>
						<td class="">{{ rtrim($item->national_pension,'0') }}</td>
						<td class="">{{ rtrim($item->health_care,'0') }}</td>
						<td class="">{{ rtrim($item->long_term_care,'0') }}</td>
						<td class="">{{ rtrim($item->employment_care,'0') }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div class="row px-0 mx-0">
				<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-success btn-sm">추가</a>
			</div>
		    <hr>
		    {{ $masterRecords->links() }}
		</div>
		
		<div class="col-md-3 order-md-1">
			@include('admin.isc_mgmt.menus')
		</div>
	</div>		
</div>
@stop

