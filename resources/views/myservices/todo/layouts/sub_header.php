<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
?><style>
.plans-subject {
	font-size: 1.1em;
}

.event-option-remove {
	color: #aa6708;
}

.event-option-complete {
	color: #a94442;
}

.event-option-detail {
	color: #1b809e;
}

body.modal-open {
	padding-right: 0 !important;
}
</style>
<script>
var current_url = "{service_url}";
</script>
<div class="container" style="padding-bottom:20px;">
	<h2>TODO 관리</h2>
	<ul class="nav nav-tabs">
		<li class="nav-item"><a class="nav-link <?php if($this->uri->segment(3)=="index"||$this->uri->segment(3)==""){echo 'active';}?>" href="{service_url}/index">할 일</a></li>
		<li class="nav-item"><a class="nav-link <?php if($this->uri->segment(3)=="view_list_completed"){echo 'active';}?>" href="{service_url}/view_list_completed">완료목록</a></li>
	</ul>
</div>