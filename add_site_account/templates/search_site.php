<table class="table table-bordered">
	<thead>
		<tr>
		  <td colspan="2" class="c-gray"><i class="icon-th-list"></i><b> List Sites</b></td>
		</tr>
		<tr>
		  <td class="c-gray"><b> ID</b></td>
		  <td class="c-gray"><b> Site</b></td>
		</tr>
	</thead>
	<tbody>

	<?php if(is_array($response)): ?>
		<?php foreach($response as $site): ?>
			<tr>
			  <td><?= $site->siteId ?></td>
			  <td><?= $site->defaultDisplayName ?> <button class="btn pull-right" id="btn_add_site"  name="btn_add_site" data-siteId="<?= $site->siteId ?>">Add</button></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
			<tr>
			  <td colspan="2"><b>No results</b></td>
			</tr>
	<?php endif; ?>
	</tbody>
</table>


<script>

$(function(){
	$("button[name='btn_add_site']").on("click", function(){
		var siteId = $(this).data("siteid");
		var self_btn = $(this)
		var description = self_btn.text();
		$.ajax({
			url: "<?= $baseURL ?>/get-site-login-form",
			method:'GET',
			data: {
				"filter_siteId": siteId
			},
			beforeSend:function(){
				self_btn.text("Loading...").attr("disabled","disabled");
			},
			complete: function(xhr){
				$("#container-page").html(xhr.responseText);
				self_btn.text(description).removeAttr("disabled");
			}
		});
	});

});

</script>