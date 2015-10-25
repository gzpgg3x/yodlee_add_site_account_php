<!-- Header -->
<?php if($panel_login_info["active"]): ?>
<div style="background: #F5F5F5;height: auto;width: 100%;padding: 5px;border-bottom: 4px solid #C7BFBF;background-image: linear-gradient(to bottom,#FFF,#E6E6E6);">
	<img class="logo" src="<?= $baseAssets ?>/img/logo-yodleeinteractive-transparent.png" style="margin-right:20px;">
	<span>CobrandId:  <a href="#"><?= $panel_login_info["user_info"]->userContext->cobrandId ?></a></span> |  
	<span><i class="icon-user"></i> Username: <a href="#"><?= $panel_login_info["user_info"]->loginName ?></a></span> |  
	<span>Rest URL: <a href="#"><i class="icon-globe"></i>  <?= $panel_login_info["base_url"] ?></a></span>
	<a style="margin-right: 20px;" class="pull-right btn btn-danger" href="<?= $baseURL ?>/logout"><i class="icon-off"></i> logout</a>
</div>
<?php endif; ?>

<!-- Content  -->
<div class="container-body">
	<div class="row">
		<div class="pull-left flow-column-left">
			<h4><?= $title ?></h4> <span class="c-gray"><?= $sub_title ?></span>
			<hr>
			<!-- Search Form for site  -->
			<div class="navbar-inner filter-site w-250">
				<form method="POST" class="navbar-form pull-left" id="search_site">
					<input type="text" value="" placeholder="Search site" name="search_site" class="span2">
					<button type="submit" id="btn_search_site" name="btn_search_site" class="btn m-top-5 btn-primary">Search Site</button>
				</form>
			</div>
			<br>

			<!-- Place Holder for subsequent flow views -->
			<div id="container-page"></div>
		</div>

		<!-- Right side: Api Logger -->
		<div class="pull-right flow-column-right">
			<div class="logger">
				<h4 class="pull-left">API Logger</h4>
				<a href="#" class="btn btn-link pull-right btnClearLogger">Clear Log</a>
				<div class="clearfix"></div>
				<ol class="rounded-list" id="accordion_log"></ol>
			</div>
		</div>
	</div>
</div>

<!-- Events Js for the current Page -->
<script type="text/javascript">
		$(function(){

			// Event to Search a site
			$("#btn_search_site").click(function(ev){
				ev.preventDefault();
				var site = $("input[name='search_site']").val();
				$("#btn_search_site").text("Loading...").attr("disabled", "disabled");
				$("#container-page").load("<?= $baseURL ?>/search-site", {filter_site: site}, function(){
					$("#btn_search_site").text("Search Site").removeAttr("disabled");
				});
			});

			// Api logger: relate logic
			$(document).ajaxComplete(function( event,request, settings ) {
			  if(_.isUndefined(settings.logger)){
			  	$.startLogger();
			  }
			});

			$("a.btnClearLogger").click(function(){
				$("div.logger").find("#accordion_log").unbind();
				$("div.logger").find("#accordion_log").empty();
			});

			$.startLogger = function(){
				$.ajax({
					url: "<?= $baseURL ?>/check-logger",
					method:'GET',
					async: true,
					logger:true,
					success: function(response){
						var attributes, logs, log,key_reference, model_log={};
						if(response!=""){
							logs = JSON.parse(response);

							for(key in logs){
								log = logs[key];
								key_reference = key;
								model_log = new App.Models.Log();
								model_log.set("method", log.method || "POST");
								model_log.set("key", key);
								model_log.set("long_url", log.long_url);
								model_log.set("short_url", log.short_url);

								try{
									model_log.set("request", JSON.parse(log.request));
								}catch(e){
									model_log.set("request", log.request);
								}

								try{
									model_log.set("response", JSON.parse(log.response));
								}catch(e){
									model_log.set("response", log.response);
								}

								if(App.Instances.Collection.Logs.where({"key":key}).length==0){
									App.Instances.Collection.Logs.add(model_log);
								}
							}
						}
					}
				});
			}
		});
</script>

<!-- Begin: Base Window Modal to show the details of the Api Logger -->
<div id="win_modal" class="yodlee modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<div class="s-title-log" id="myModalLabel">
			<span class="method_expand c-gray"></span> <span class="short_url_expand"><span> 
		</div>
	</div>
	<div class="modal-body"></div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>
<!-- End: Base Window Modal to show the details of the Api Logger -->

<!-- Begin: Templates Using Underscore for the Api Logger -->
<script type="text/template" id="template_modal">
	<p class="font-size-10"><strong>Url:</strong> <span class="c-blue"><%= long_url %></span></p>
	<div class="span6 no-margin-left">
		<div><strong>Request: </strong> <span class="timer-expand"><%= request.timer %><span></div>
		<textarea class="request-expanded"></textarea>
	</div>
	<div class="span5 no-margin-left">
		<div><strong>Response: </strong> <span class="timer-expand"><%= response.timer %><span></div>
		<textarea class="response-expanded"></textarea>
	</div>
</script>

<script type="text/template" id="template_panel_log">
	<div class="accordion-heading">
		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_log" href="#">
			<div class="pull-left p-left-15 s-title-log">
				<span class="method c-gray"><%= method %>:</span> <span title="<%= long_url %>" class="short_url"><%= (short_url.length>55) ? short_url.substring(0,55)+"..." : short_url %><span> 
			</div>
			<button class="btn pull-right">&#43;</button>
			<div class="clearfix"></div>
		</a>
	</div>
	<div id class="accordion-body collapse">
		<div class="accordion-inner">
			<input type="hidden" name="key" value="<%= key %>">
			<div class="log-request pull-left">
				<div><strong>Request: </strong> <span class="timer"><%= request.timer %><span></div>
				<div class="body-log">
					<div class="request"></div>
				</div>
			</div>
			<div class="log-response pull-right">
				<div><strong>Response: </strong> <span class="timer"><%= response.timer %><span></div>
				<div class="body-log">
					<div class="response"></div>
				</div>
			</div>
			<div class="clearfix"></div>
			<input type="button" class="btn-expand-detail pull-right m-top-5 m-bottom-15 btn btn-warning" value="...">
		</div>
	</div>
</script>
<!-- End: Templates Using Underscore for the Api Logger -->

<!-- Loading Views, Collections, and Models for the Api Logger using Backbone -->
<script src="<?= $baseAssets ?>/js/logger.js"></script>