<style>
table { border-collapse: collapse; font-family: Futura, Arial, sans-serif; border: 1px solid #777; }
caption { font-size: larger; margin: 1em auto; }
th, td { padding: .65em; }
th, thead { background: #000; color: #fff; border: 1px solid #000; }
tr:nth-child(odd) { background: #ccc; }
tr:hover { background: #aaa; }
td { border-right: 1px solid #777; }

.hidden {
	opacity: 0;
}
</style>

<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>

<a href="?">Go Back!</a>
<hr/>

<table>
	<thead>
		<th>Username</th>
		<th>Alias</th>
		<th>Realm</th>
		<th>Password</th>
		<th>Actions</th>
	</thead>
	<tbody>
	<tr>
		<td><input placeholder="John.Doe.JR" type="text" id="username"/></td>
		<td><input placeholder="John Doe Junior" type="text" id="alias"/></td>
		<td><input placeholder="chat.example.com" type="text" id="realm"/></td>
		<td><input placeholder="*********" type="password" id="password"/></td>
		<td><button onclick="javascript:addUser()">Add</button></td>
	</tr>
<?php foreach($users as $user): ?>
	<tr>
		<td><?php echo $user['username']; ?></td>
		<td><?php echo $user['alias']; ?></td>
	 	<td><?php echo $user['realm']; ?></td>
		<td class="hidden"><?php echo $user['password']; ?></td>
		<td>
			<button><a href="?action=userHide&username=<?php echo urlencode($user['username']); ?>">Hide</a></button>
			<button><a href="?action=userShare&username=<?php echo urlencode($user['username']); ?>">Share</a></button>
			<button><a href="?action=userDelete&username=<?php echo urlencode($user['username']); ?>">Delete</a></button>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<script>
$().ready(function() {
	$(".hidden").on("mouseenter mouseleave", function(e) {
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
