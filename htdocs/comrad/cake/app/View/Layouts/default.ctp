<?php require_once 'initialize.php' ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo $this->Html->charset(); ?>
		<title><?php echo 'Music Library' ?> - <?php echo $title_for_layout; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="KGNU">
		<meta name="author" content="KGNU">
		<?php echo $this->Html->css('style'); ?>
		
		<?php echo $this->Html->css('bootstrap'); ?>
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>
		<?php echo $this->Html->css('bootstrap-responsive'); ?>
		
		<?php echo $this->fetch('css'); ?>
		
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<?php echo $this->Html->meta('icon'); ?>
		<!-- <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png"> -->
		<!-- <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png"> -->
		<!-- <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png"> -->
	</head>
	
	<body>
		
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<?php echo $this->Html->link(Configure::read('Organization.Name'), 'http://kgnu.org/playlist/contents.php', array('class' => 'brand')); ?>
					<div class="nav-collapse">
						<ul class="nav">
							<!--
							<li><?php echo $this->Html->link('Show Builder', array('controller' => 'showbuilder', 'action' => 'upcomingshows')) ?></li>
							<li><?php echo $this->Html->link('Schedule', array('controller' => 'schedule', 'action' => 'index')) ?></li>
							<li class="active"><?php echo $this->Html->link('Music Library', array('controller' => 'music_library', 'action' => 'index')) ?></li>
							<li><?php echo $this->Html->link('Admin', array('controller' => 'events', 'action' => 'index')) ?></li>
							-->
							<li><?php echo $this->Html->link('Show Builder', 'http://kgnu.org/playlist/findshows.php') ?></li>
							<li><?php echo $this->Html->link('Schedule', 'http://kgnu.org/playlist/calendar.php') ?></li>
							<li><?php echo $this->Html->link('Music Library', array('controller' => 'music_library', 'action' => 'index')) ?></li>
							<li><?php echo $this->Html->link('Events', 'http://kgnu.org/playlist/events.php') ?></li>
							<!--<li><?php //echo $this->Html->link('Hosts', array('controller' => 'hosts', 'action' => 'index')); ?></li>-->
						</ul>
						<ul class="nav pull-right">
							<!--<li><?php echo $this->Html->link('Help', array('controller' => 'help', 'action' => 'index')) ?></li>-->
							<li><?php echo $this->Html->link('Help', 'http://kgnu.org/playlist/about.php') ?></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Logged in as <?php echo $_SESSION['Username'] ?> <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="http://kgnu.org/playlist/index.php?signout=1">Log Out</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($this->fetch('subNavLinks')): ?>
		<div class="subnav">
			<div class="container-fluid">
				<ul class="nav nav-pills">
					<?php echo $this->fetch('subNavLinks'); ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="container-fluid">
			<?php $crumbs = $this->Session->read('Breadcrumb.crumbs'); ?>
			<?php if (count($crumbs) > 1): ?>
				<div class="row-fluid">
					<div class="span12">
						<ul class="breadcrumb">
							<?php foreach ($crumbs as $i => $crumb): ?>
								<?php if ($i < count($crumbs) - 1): ?>
									<li>
										<a href="<?php echo $crumb['url'] ?>"><?php echo $crumb['title'] ?></a>
										<span class="divider">/</span>
									</li>
								<?php else: ?>
									<li class="active">
										<?php echo $crumb['title']?>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="row-fluid">
				<div class="span12">
					<?php echo $this->TB->flashes(array('closable' => true)); ?>
				</div>
			</div>
			
			<?php echo $content_for_layout; ?>
			
			<hr>
			
			<footer>
				<p>Built with CakePHP v<?php echo Configure::version() ?></p>
			</footer>
			
		</div><!--/.fluid-container-->
		
		<?php echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js'); ?>
		<?php echo $this->Html->script('bootstrap'); ?>
		<?php echo $this->fetch('script'); ?>

	</body>
</html>
