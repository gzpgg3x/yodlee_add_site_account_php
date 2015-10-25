<img src="<?= $baseAssets ?>/img/logo-yodleeinteractive-transparent.png"> <img src="<?= $baseAssets ?>/img/loading.gif"> <?= $site_info->defaultDisplayName ?>
<br><br>
<span id="message"></span>

<script type="text/javascript">
var site_Refresh_Info_Interval = null;
var memSiteAccId = '<?= $memSiteAccId ?>';

$(function(){
	function siteRefreshInfo(){
		$.ajax({
			url: "<?= $baseURL ?>/get-site-refresh-info",
			cache:false,
			method:'POST',
			data: {
				"memSiteAccId": memSiteAccId
			},
			complete: function(xhr, status){
				var response = xhr.responseText;
				if(response!=""){
					clearInterval(site_Refresh_Info_Interval);
					$("#container-page").html(response);
				}
			} // end complete: function(xhr, status){
		}); // end $.ajax

	} // end function siteRefreshInfo()

	function startSiteRefreshInfo(){
		$("#message").text("Sending data...");
		site_Refresh_Info_Interval = setInterval(siteRefreshInfo, 4000);
	}

	startSiteRefreshInfo();
});
</script>