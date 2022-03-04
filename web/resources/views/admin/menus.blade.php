<h4>환경 설정</h4>
<ul class="list-group">
    <a href="/admin"
	class="list-group-item text-decoration-none @if ($current == '') active @endif"
	>설정</a>
	<a href="/admin/folderMgmt"
	class="list-group-item text-decoration-none @if ($current == 'folder-control') active @endif"
	>폴더 설정</a>
    <a href="/admin/version"
	class="list-group-item text-decoration-none @if ($current == 'version') active @endif"
	>버전 정보</a>
</ul>
