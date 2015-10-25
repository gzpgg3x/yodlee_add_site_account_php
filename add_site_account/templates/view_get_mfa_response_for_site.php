<img src="<?= $baseAssets ?>/img/logo-yodleeinteractive-transparent.png"> <img src="<?= $baseAssets ?>/img/loading.gif"> <?= $site_info->defaultDisplayName ?>
<br><br>
<span id="message"></span>

<script type="text/javascript">
var Mfa_Response_For_Site_Interval = null;
var memSiteAccId = '<?= $memSiteAccId ?>';

$(function(){
	
	function getMfaResponseForSite() { 
		$.ajax({
			url: "<?= $baseURL ?>/get-mfa-response-for-site",
			cache:false,
			method:'POST',
			data: {
				"memSiteAccId": memSiteAccId
			},
			complete: function(xhr, status){
				var response = xhr.responseText;
				if(response!=""){
					clearInterval(Mfa_Response_For_Site_Interval);
					$("#container-page").html(response);
				}
			}
		});
	}

	function startGetMfaResponseForSite(){
		$("#message").text("Loading information...");
		Mfa_Response_For_Site_Interval = setInterval(getMfaResponseForSite, 2000);
	}

	startGetMfaResponseForSite();
});
</script>