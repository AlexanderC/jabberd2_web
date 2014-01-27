<?php @ob_end_flush(); ob_start(); ?>

<a href="?">Go Back!</a>
<hr/>

<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
            <th>Username</th>
            <th>Alias</th>
            <th>Realm</th>
            <th>Password</th>
            <th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td><input class="span2" placeholder="John.Doe.JR" type="text" id="username"/></td>
		<td><input class="span2" placeholder="John Doe Junior" type="text" id="alias"/></td>
		<td><input class="span2" placeholder="chat.example.com" type="text" id="realm"/></td>
		<td><input class="span2" placeholder="*********" type="password" id="password"/></td>
		<td><button class="btn" onclick="javascript:addUser()">Add</button></td>
	</tr>
    <?php foreach($users as $user): ?>
	<tr>
		<td><?php echo $user['username']; ?></td>
		<td><?php echo $user['alias']; ?></td>
	 	<td><?php echo $user['realm']; ?></td>
		<td class="op-hidden"><?php echo $user['password']; ?></td>
		<td>
			<button class="btn"><a href="?action=userHide&username=<?php echo urlencode($user['username']); ?>">Hide</a></button>
			<button class="btn"><a href="?action=userShare&username=<?php echo urlencode($user['username']); ?>">Share</a></button>
			<button class="btn"><a href="?action=userDelete&username=<?php echo urlencode($user['username']); ?>">Delete</a></button>
		</td>
	</tr>
    <?php endforeach; ?>
	</tbody>
</table>

<?php $content = ob_get_clean(); ?>

<?php @ob_end_flush(); ob_start(); ?>

<script>
$().ready(function() {
	$(".op-hidden").on("mouseenter mouseleave", function(e) {
		if(e.type == "mouseenter") $(this).animate({opacity: 1}, "fast");
		else $(this).animate({opacity: 0}, "fast");
	});
});

function addUser()
{
	var url = "?action=userAdd&username=:username:&alias=:alias:&realm=:realm:&password=:password:"
		.replace(":username:", encodeURIComponent($("input#username").val()))
		.replace(":alias:", encodeURIComponent($("input#alias").val()))
		.replace(":realm:", encodeURIComponent($("input#realm").val()))
		.replace(":password:", encodeURIComponent($("input#password").val()));

	window.location = url;
}
</script>

<?php $scripts = ob_get_clean(); ?>

<?php
    $tpl = require __DIR__ . "/layout.php";
    echo str_replace(array(
                        '%content%',
                        '%scripts%'
                     ), array(
                        $content,
                        $scripts
                     ), $tpl);
?>

