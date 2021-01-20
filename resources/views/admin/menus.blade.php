<ul class="list-group">
	<a href="/admin/archiveMgmt" 
		class="list-group-item @if ($current == 'archive-control') active @endif"
	>아카이브 설정</a>
	<a href="/admin/folderMgmt" 
	class="list-group-item @if ($current == 'folder-control') active @endif"
	>폴더 설정</a>
	<a href="/admin/advanced" 
	class="list-group-item @if ($current == 'advanced') active @endif"
	>고급 기능</a>
</ul>