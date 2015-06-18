<?php
	require_once dirname(__FILE__) . '/dl-api/dl-api.php';
	$DLApi = new DLApi(array('apiKey' => '8eeef276f83d3b17de0e2d31530400b6'));
	
	$DLApi->run();
	
	/**
	 * TODO: Category jumps
	 * 
	 * When a category is filtered we need to cherry pick out the products that belong to that category and only show those.
	 */
	 
	/**
	if(isset($_GET['cat'])) {
		$filterCategory = $_GET['cat'];
	}
	**/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="">
		<title>District Lines - cURL API</title>
	
		<!-- Bootstrap -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>`
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<div id="menu-container">
		  		<div class="shop-sub-nav">
		  			<?php foreach($DLApi->categories as $items) { foreach($items as $i => $v) { if($v->parent == '') { continue; }?>
			  			<a class="mast_links" href="?cat=<?= urlencode($v->parent) ?>"><?= $v->parent ?></a>
		  			<?php }} ?>
		  		</div>
		  	</div>
		</header>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<?php foreach($DLApi->products as $items) { foreach($items as $p => $v) { ?>
					<?php /* if(isset($filterCategory) && $v->subsubcategory != $filterCategory) { continue; } */ ?>
					<div class="product-item">
						<div class="image-container">
							<span class="price">$<?= $v->on_sale == 1 ? $v->purchase_sale_price : $v->purchase_price ?></span>
							<img src="<?= $DLApi->cdnURL ?><?= $v->product_id ?>/<?= $v->image ?>" width="200" />
						</div>
						<p><?= $DLApi->truncate($v->product, 35) ?></p>
						<a target="_blank" href="http://www.districtlines.com/<?= $v->product_id . '-' . $v->product_clean ?>/<?= $v->vendor_clean ?>">Buy Now!</a>
					</div>
					<?php }} ?>
				</div>
			</div>
		</div>
	
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	</body>
</html>