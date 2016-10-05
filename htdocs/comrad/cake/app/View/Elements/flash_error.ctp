<strong>Error!</strong>
<?php echo $message ?>
<?php if (isset($link_text) && isset($link_url)): ?>
	<?php echo $this->Html->link($link_text, $link_url) ?>
<?php endif; ?>
<?php if (isset($details)): ?>
	<pre><?php echo $details ?></pre>
<?php endif; ?>