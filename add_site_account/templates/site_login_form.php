<?php if(isset($_SESSION["error_msg"]) && $_SESSION["error_msg"] != ""): ?>
	<?php if(count($_SESSION["error_msg"])>0): ?>
		<?php foreach ($_SESSION["error_msg"] as $error): ?>
			<div class="alert alert-error">
				<?= $error ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php $_SESSION["error_msg"] = ""; ?>
<?php endif; ?>

Please enter the same credentials you use to access your <b><?= $site_info->defaultDisplayName ?></b> account online.
<div class="navbar-inner">
	<div>
		<form method="POST" id="site_login_form" class="form-inline form-horizontal m-top-15">
			<input type="hidden" name="siteId" value="<?= $siteId ?>">
			<div class="control-group">
				<label class="control-label">Login</label>
				<div class="controls">
					<input type="text" id="login_form" name="login" placeholder="Enter Login" value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Password</label>
				<div class="controls">
					<input type="password" id="password_form" name="password" placeholder="Enter Password" value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Confirm Password</label>
				<div class="controls">
					<input type="password" id="confirm_password_form" name="confirm_password" placeholder="Confirm Password" value="">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-send-data btn-primary">Add</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
$(function(){
	$(".btn-send-data").click(function(ev){
		ev.preventDefault();

		if($("#login_form").val().trim()==""){
			return false;
		}

		if($("#password_form").val().trim()==""){
			return false;
		}

		if($("#confirm_password_form").val().trim()==""){
			return false;
		}

		if($("#password_form").val()!=$("#confirm_password_form").val()){
			return false;
		}

		var values = $("#site_login_form").serializeArray();
		var params = {};
		for(value in values) { 
			params[values[value].name] = values[value].value; 
		}
		var self_btn = $(".btn-send-data")
		var description = self_btn.text();
		$(".btn-send-data").text("Loading...").attr("disabled","disabled");
		$("#container-page").load("<?= $baseURL ?>/add-site-account1", params, function(){
			self_btn.text(description).removeAttr("disabled");
		});
	});
});
</script>
