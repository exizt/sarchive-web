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
			<div class="table-responsive">
				<table class="table table-striped table-hover table-dark table-sm">
					<thead class="thead-inverse">
						<tr>
							<th>구분</th>
							<th colspan="2">월급여 (-비과세)<br>(천 단위)</th>
							<th colspan="11" class="text-center">공제 대상 가족 수</th>
						</tr>
						<tr>
							<th>해당년월</th>
							<th>시작액</th>
							<th>종료액</th>
							<th>(구성원 1)</th>
							<th>(구성원 2)</th>
							<th>(구성원 3)</th>
							<th>(구성원 4)</th>
							<th>(구성원 5)</th>
							<th>(구성원 6)</th>
							<th>(구성원 7)</th>
							<th>(구성원 8)</th>
							<th>(구성원 9)</th>
							<th>(구성원 10)</th>
							<th>(구성원 11)</th>
						</tr>
					</thead>
					<tbody>
					@foreach ($masterRecords as $item)
						<tr>
							<td class=""><a href="{{ route($ROUTE_ID.'.edit',$item->id) }}" class="text-light text-decoration-none">{{ $item->yearmonth }}</a></td>
							<td class="">{{ $item->money_start }}</td>
							<td class="">{{ $item->money_end }}</td>
							<td class="">{{ number_format($item->tax_1) }}</td>
							<td class="">{{ number_format($item->tax_2) }}</td>
							<td class="">{{ number_format($item->tax_3) }}</td>
							<td class="">{{ number_format($item->tax_4) }}</td>
							<td class="">{{ number_format($item->tax_5) }}</td>
							<td class="">{{ number_format($item->tax_6) }}</td>
							<td class="">{{ number_format($item->tax_7) }}</td>
							<td class="">{{ number_format($item->tax_8) }}</td>
							<td class="">{{ number_format($item->tax_9) }}</td>
							<td class="">{{ number_format($item->tax_10) }}</td>
							<td class="">{{ number_format($item->tax_11) }}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
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

