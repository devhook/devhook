<!DOCTYPE html>
<html>
<head>
	<title>Bootstrap 101 Template</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap -->
	<link href="/packages/devhook/devhook/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="/packages/devhook/devhook/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">

	<script src="/packages/devhook/devhook/js/jquery.js"></script>
</head>
<body>

<?=AdminUI::navbar() ?>

<div class="navbar navbar-static-top navbar-inverse size-sm">
	<div class="container">
		<ul class="nav navbar-nav pull-left">
			<li><a href="#">Доставка и оплата</a></li>
			<li><a href="#">Отзывы</a></li>
			<li><a href="#">Условия и гарантия</a></li>
			<li><a href="#">Помощь</a></li>
			<li><a href="#">Контакты</a></li>
		</ul>

	<? if ($user->id): ?>
			<div class="navbar-form pull-right">
				<div class="btn-group">
					<a href="<?=URL::to('account') ?>" class="btn btn-default"><?=$user->login ?></a>
					<a href="<?=URL::to('account/logout') ?>" class="btn btn-default">выйти</a>
				</div>
			</div>
		<? else: ?>
			<ul class="nav navbar-nav pull-right">
				<li><?=link_to('account/login', 'Вход') ?></li>
				<li><?=link_to('account/registration', 'Регитсрация') ?></li>
			</ul>
		<? endif ?>
	</div>
</div>

<div class="container">

	<div class="row">
		<div class="col-lg-3">
			<div style='font:50px serif'><?=Request::is('/') ? 'Ya-Yo' : link_to('/', 'Ya-Yo') ?></div>
		</div>

		<div class="col-lg-6">

		</div>

		<div class="col-lg-3">
			<div class="well">
				<div><?=link_to('cart', 'Ваша корзина') ?></div>
				<div><?=Cart::count() ?> товара на <?=Cart::total() ?> р.</div>
			</div>
		</div>
	</div>

	<div class="navbar navbar-default">
		<ul class="nav navbar-nav">
			<li class="dropdown">
				<a href="#" data-toggle="dropdown">Все категории <span class="caret"></span></a>
					<?=Cache::get('root_categories', function(){
						$categories = Category::getByParent(0);
						$result     = '<ul class="dropdown-menu">';
						foreach ($categories as $cat) $result .= "<li><a href='".$cat->link()."'>{$cat->title}</a></li>";
						$result .= '</ul>';
						Cache::add('root_categories', $result, 30);
						return $result;
					}) ?>
			</li>
			<li><a href="#">Возраст</a></li>
			<li><a href="#">Мальчикам</a></li>
			<li><a href="#">Девочкам</a></li>
			<li><a href="#">Персонажи</a></li>
			<li><a href="#">Бренды</a></li>
		</ul>

		<form class="navbar-form pull-right">
			<input type="text" class="form-control" style="width: 200px;">
			<button type="submit" class="btn btn-default">Найти</button>
		</form>
	</div>

	<div id="content">
		<?=$content ?>
	</div><!--#content-->

</div><!--.container-->

<script src="/packages/devhook/devhook/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>