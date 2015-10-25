<div class="navbar-inner">
	<div class="m-top-5">
		<h4><?= $site_info->defaultDisplayName ?></h4>
		<br>
		<div class="default-panel">Please enter your <b><?= $site_info->defaultDisplayName ?></b> security information.</div>
		<br>
		<form id="form_mfa" class="form-horizontal">
			<input type="hidden" name="memSiteAccId" value="<?= $memSiteAccId ?>">
				<div class="control-group">
					<label class="control-label"><strong><?= $params->fieldInfo->displayString ?></strong></label>
					<div class="controls">
						<input type="text" name="token" id="token">
					</div>
				</div>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-send-data">Next</button>
				</div>
			</div>
		</form>
		<br>
		<b><span class="c-black-bold" id="seconds"></span></b>
	</div>
</div>

<script type="text/javascript">
	var seconds = '<?= (int)($params->timeOutTime/1000) ?>';
	var timeout_default = null;
	$(function(){
		$(".btn-send-data").click(function(ev){
			ev.preventDefault();

			if($("#token").val().trim()==""){
				return false;
			}

			clearInterval(timeout_default);

			fields = $("#form_mfa").serialize();
			var description = $(".btn-send-data").text();
			$.ajax({
				url: "<?= $baseURL ?>/put-mfa-request-for-site",
				cache:false,
				method:'POST',
				data: fields,
				beforeSend: function(){
					$(".btn-send-data").text("Loading...").attr("disabled","disabled");
				},
				complete: function(xhr, status){
					var response = xhr.responseText;
					if(response!=""){
						$("#container-page").html(response);
					}
					$(".btn-send-data").text(description).removeAttr("disabled");
				}
			});
		});

		function renderSecondsTimeout(){
			$("#seconds").text(seconds+" seconds...");
			if(seconds<=15){
				if(!$("#seconds").is(".c-red-bold")){
					$("#seconds").removeClass("c-black-bold").addClass("c-red-bold");
				}
			}
			if(seconds==0){
				clearInterval(timeout_default);
				$("#container-page").load("<?= $baseURL ?>/timeOut");
			}else{
				seconds--;
			}
		}
		renderSecondsTimeout();
		var timeout_default = setInterval(renderSecondsTimeout,1000);
	});
</script>