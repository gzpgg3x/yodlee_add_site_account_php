<?php if(is_array($response)): ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<td colspan="2" class="c-gray"><i class="icon-th-list"></i><b> Add Account</b></td>
			</tr>
			<tr>
				<td class="c-gray"><b> Added Account(s)</b></td>
			</tr>
		</thead>
		<tbody>
			<?php if(count($response)>0): ?>
				<?php foreach($response as $item): ?>
					<tr>
						<td>
							<img src="<?= $baseAssets ?>/img/siteImage.fastlinksb.gif"> <?= $item->itemDisplayName ?>
							<?php if(isset($item->itemData)): ?>
								<?php if(count($item->itemData->accounts)>0): ?>
									<?php if(isset($item->itemData->accounts[0]->totalBalance)): ?>
										 <?php 
										 	$amount = $item->itemData->accounts[0]->totalBalance->amount;
										 	$currencyCode = $item->itemData->accounts[0]->totalBalance->currencyCode;
										 ?>
										 <span class="pull-right"><?= sprintf("%s- %s",$currencyCode, number_format($amount,2,',','.')) ?></span>
									<?php else: ?>
										<span class="pull-right">--</span>
									<?php endif; ?>
								<?php else: ?>
									<span class="pull-right">--</span>
								<?php endif; ?>
							<?php else: ?>
								<span class="pull-right">--</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="2"><b>No results</b></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
<?php else: ?>
	No results
<?php endif; ?>