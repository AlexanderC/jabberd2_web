<?php @ob_end_flush(); ob_start(); ?>

<h1>
	Control Panel <br/>
	<small style="color:#ccc;font-size:0.5em;">Please <a href="?action=install">install</a> if action not yet performed.</small>
</h1>

<ul>
	<li>
		<a href="?action=users">Manage Users</a>
	</li>
    <li>
        <a href="?action=sendMessage">Send Message</a>
    </li>
</ul>

<?php $content = ob_get_clean(); ?>

<?php
$scripts = "";

$tpl = require __DIR__ . "/layout.php";
echo str_replace(array(
                      '%content%',
                      '%scripts%'
                 ), array(
                         $content,
                         $scripts
                    ), $tpl);
?>
