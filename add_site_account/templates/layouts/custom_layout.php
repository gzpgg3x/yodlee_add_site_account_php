<!DOCTYPE html>
<html>
<head>
	<title><?= $title ?></title>
	<!-- Bootstrap -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?= $baseAssets ?>/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="<?= $baseAssets ?>/css/main.css" rel="stylesheet" media="screen">
	<script src="<?= $baseAssets ?>/js/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" href="<?= $baseAssets ?>/css/codemirror.css">
	<link rel="stylesheet" href="<?= $baseAssets ?>/css/eclipse.css">
	<link rel="stylesheet" href="<?= $baseAssets ?>/css/logger-list-details.css">

	<script src="<?= $baseAssets ?>/js/codemirror.js"></script>
	<script src="<?= $baseAssets ?>/js/javascript.js"></script>
	<script src="<?= $baseAssets ?>/js/bootstrap.min.js"></script>

	<script src="<?= $baseAssets ?>/js/underscore-min.js"></script>
	<script src="<?= $baseAssets ?>/js/underscore.string.min.js"></script>
	<script src="<?= $baseAssets ?>/js/backbone-min.js"></script>
	<script src="<?= $baseAssets ?>/js/jquery.codemirror.js" type="text/javascript"></script>
</head>
<body>
	<script type="text/javascript">
		var App = {
			baseUrl: '<?= $baseURL ?>',
			Views:{},
			Models:{},
			Collections:{},
			Instances:{
				Collection:{},
				Model:{}
			},
			Output: {}
		};
	</script>
	<?= $content ?>
</body>
</html>