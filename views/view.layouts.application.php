<!DOCTYPE html>
<html>
	<head>
		<title>Brian Seitel | Web Application Developer</title>
		<meta charset="utf-8"/>
		<link href='http://fonts.googleapis.com/css?family=Convergence' rel='stylesheet' type='text/css'>
		{{css_stuff}}
	</head>
	<body class="<?= $controller ?> <?= $view ?>">
		<div id="wrapper">
			<?= render('master', 'header') ?>

			<?= render($controller, $view); ?>

			<?= render('master', 'footer'); ?>
		</div>
		{{javascript_stuff}}
	</body>	
</html>