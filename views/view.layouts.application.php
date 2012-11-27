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

		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-4459653-3']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	</body>	
</html>