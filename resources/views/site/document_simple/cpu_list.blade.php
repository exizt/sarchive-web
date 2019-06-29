@extends('layouts.service')
@section('content')
<div class="container">
	<h1>CPU LIST</h1>
	<div style="margin: 0 15px 0 15px;">
		<div style="display:none">
			<button type="button" class="btn btn-primary" data-toggle="collapse"
				data-target="#collapseExample" aria-expanded="false"
				aria-controls="collapseExample">상세 검색</button>
			<div class="collapse in" id="collapseExample">
				<div class="">
					<p>검색조건</p>
					<h3>코어형태</h3>
					<div class="well">
						<label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox1" value="option1"> 듀얼코어
						</label> <label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox2" value="option2"> 쿼드코어
						</label> <label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox3" value="option3"> 옥타코어
						</label>
					</div>
					<h3>쓰레드</h3>
					<div class="well">
						<label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox1" value="option1"> 2개
						</label> <label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox2" value="option2"> 4개
						</label> <label class="checkbox-inline"> <input type="checkbox"
							id="inlineCheckbox3" value="option3"> 8개
						</label>
					</div>
				</div>
			</div>
		</div>
		<p>DataList Count({{$total_rows}})</p>
		<table class="table table-hover table-striped table-responsive"
			id="task-inbox">
			<thead class="thead-inverse">
				<tr>
					<th>제품명</th>
					<th>코어</th>
					<th>쓰레드</th>
					<th>클럭</th>
					<th>클럭 Max</th>
					<th>코드네임</th>
					<th>소켓</th>
					<th>TDP</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($dataset as $item)
				<tr>
					<td>{{$item->product_name}}</td>
					<td>{{$item->core_num}}</td>
					<td>{{$item->thread_num}}</td>
					<td>{{$item->base_clock}} GHz</td>
					<td>{{$item->max_clock}} GHz</td>
					<td>{{$item->code_name}}</td>
					<td>{{$item->socket_type}}</td>
					<td>{{$item->tdp}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<nav aria-label="Page navigation">{{ $dataset->links() }}</nav>
		
	</div>
</div>
@stop