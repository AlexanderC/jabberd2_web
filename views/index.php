<?php $buffer->listen(); ?>

<h1>
	Control Panel <br/>
	<small style="color:#ccc;font-size:0.5em;">Please <a href="?action=install">install</a> if action not yet performed.</small>
</h1>

<ul>
	<li>
		<a href="?action=users">Manage Users</a>
	</li>
    <li>
        <a href="?action=sendMessage">Broadcast Message</a>
    </li>
</ul>

<?php $buffer->collect("content"); ?>

<?php echo $buffer; ?>
