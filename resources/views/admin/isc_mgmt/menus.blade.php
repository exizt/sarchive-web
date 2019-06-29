<h5>실수령액 계산기 관리</h5>
<div class="list-group px-3 shhNav-RouteIdUsed">
	<a href="{{ route('admin.isc_termMgmt.index') }}" class="list-group-item list-group-item-action list-group-item-secondary" data-id="admin.isc_termMgmt">용어 사전 관리</a>
	<a href="{{ route('admin.isc_rateMgmt.index') }}" class="list-group-item list-group-item-action list-group-item-secondary" data-id="admin.isc_rateMgmt">세율 조정</a>
	<a href="{{ route('admin.isc_incomeTaxMgmt.index') }}" class="list-group-item list-group-item-action list-group-item-secondary" data-id="admin.isc_incomeTaxMgmt">소득세 간이세액표 확인</a>
</div>
<script>
$(function() {
	$(".shhNav-RouteIdUsed a[data-id='"+ROUTE_ID+"']").addClass("active");
});
</script>