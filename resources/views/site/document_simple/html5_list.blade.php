@extends('layouts.service') @section('content')
<div class="container">
	<h1>HTML5</h1>
	<h5>html5 의 정보 를 모아둠</h5>
	<div style="margin: 0 15px 0 15px;">

		<p>DataList Count({{$total_rows}})</p>
		<table class="table table-hover table-striped table-responsive"
			id="task-inbox">
			<thead class="thead-inverse">
				<tr>
					<th>태그</th>
					<th>html5 여부</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($dataset as $item)
				<tr>
					<td>{{$item->subject}}</td>
					<td>{{$item->is_html5}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<nav aria-label="Page navigation">{{ $dataset->links() }}</nav>
	</div>
</div>
@stop
