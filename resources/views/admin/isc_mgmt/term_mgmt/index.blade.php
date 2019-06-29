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
			<table class="table table-striped table-hover">
				<thead class="thead-inverse">
					<tr>
						<th>#</th>
						<th>명칭 <code>(클릭시 바로 수정 화면)</code></th>
						<th>보기</th>
						<th>변경일시</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($masterRecords as $item)
					<tr>
						<th scope="row">{{ $item->id }}</th>
						<td class=""><a href="{{ route($ROUTE_ID.'.edit',$item->id) }}" class="text-dark">{{ $item->name }}</a></td>
						<td class=""><a href="{{ route($ROUTE_ID.'.show',$item->id) }}" class="text-dark">내용 보기</a></td>
						<td class="">{{ $item->updated_at }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div class="row px-0 mx-0">
				<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-success btn-sm">신규</a>
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

