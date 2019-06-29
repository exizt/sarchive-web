/*
@ver 1.0.180603
*/
/* create download link of latest release use example)
  GitHub_getLatestReleaseZipDownloadLink("username/reponame",function(link){
    $("a.downlink-latest-release").attr("href",link);
  });
*/
/**
 * get zip file link
 * @param repo
 * @param after
 * @returns
 */
function GitHub_getLatestReleaseDownload(repo,after){
	GitHub_getLatestReleaseDownloadLink(repo,after,'zip');
}
/**
 * get tar link
 * @param repo
 * @param after
 * @returns
 */
function GitHub_getLatestReleaseDownload_Tar(repo,after){
	GitHub_getLatestReleaseDownloadLink(repo,after,'tar');
}
/**
 * get latest release link
 * @param repo
 * @param after
 * @param tarzip
 * @returns
 */
function GitHub_getLatestReleaseDownloadLink(repo,after,tarzip = 'zip')
{
	if(typeof repo === "undefined") return;

	GitHub_getLatestRelease(repo,function(data){
		if(tarzip=='zip'){
			var link = "https://github.com/"+repo+"/archive/" + data.tag_name + ".zip";
			//var link = data.zipball_url;
		} else {
			var link = "https://github.com/"+repo+"/archive/" + data.tag_name + ".tar.gz";
			//var link = data.tarball_url;
		}
		after(link);
	});
}
/**
 * get latest release data
 * @param repo
 * @param after
 * @returns json
 */
function GitHub_getLatestRelease(repo,after){
	if(typeof $.ajaxSettings.headers["X-CSRF-TOKEN"] !== 'undefined' && $.ajaxSettings.headers["X-CSRF-TOKEN"] !== null)
	{
		var before_csrftoken = $.ajaxSettings.headers["X-CSRF-TOKEN"];
		delete $.ajaxSettings.headers["X-CSRF-TOKEN"];
	}
	$.get('https://api.github.com/repos/'+repo+'/releases/latest', function (data) {
		if (typeof before_csrftoken !== 'undefined' && before_csrftoken !== null) {
			$.ajaxSettings.headers["X-CSRF-TOKEN"] = before_csrftoken;
		}
		after(data);
	});	
}
