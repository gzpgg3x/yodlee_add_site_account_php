<div class="navbar-inner">
	<div class="m-top-5">
		<h4><?= $site_info->defaultDisplayName ?></h4>
		<br>
		<div class="default-panel">Please enter your <b><?= $site_info->defaultDisplayName ?></b> security information.</div>
		<br>
		<form id="form_mfa" class="form-horizontal">
			<input type="hidden" name="memSiteAccId" value="<?= $memSiteAccId ?>">
			<?php foreach($params->fieldInfo->questionAndAnswerValues as $field): ?>
				<div class="control-group">
					<label class="control-label"><strong><?= $field->question ?> <?= ($field->isRequired) ? "*" : "" ?></strong></label>
					<div class="controls">
						<input type="<?= $field->responseFieldType ?>" data-required="<?= $field->isRequired ?>" name="field[<?= $field->metaData ?>]">
					</div>
				</div>
			<?php endforeach;?>
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

			var isValid=true;
			$("#form_mfa").find("input[type='text']").each(function(){ 
				var isRequired = $(this).data("required"); 
				var value = $(this).val().trim(); 
				if(isRequired==true && value=="") {
					isValid=false;
				}
			});
			
			if(!isValid){
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


// var list_errors = [];var error = {code:"",type:"",name:"", description:""};$("#list").find("tbody tr").each(function(){ error.code= $(this).find("td").eq(0).text();error.type= $(this).find("td").eq(1).text();error.name= $(this).find("td").eq(2).text();error.description= $(this).find("td").eq(3).text(); list_errors.push(error) });
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