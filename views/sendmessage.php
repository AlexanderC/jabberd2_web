<?php $buffer->listen(); ?>

<a href="?">Go Back!</a>
<hr/>

<form action="?action=sendMessage" method="POST">
    <label> Select Users
        <select multiple size="2" name="users[]" class="input-lg input-group height-200">
            <?php foreach($users as $user): ?>
                <option value="<?php echo sprintf("%s@%s", $user['username'], $user['realm']); ?>">
                    <?php echo $user['alias']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label> Select Conferences <SOMETIMES NOT WORKING>
        <select multiple size="2" name="conferences[]" class="input-lg input-group height-200">
            <?php foreach($channels as $channel): ?>
            <option value="<?php echo $channel['name']; ?>">
                <?php echo $channel['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <textarea name="text" class="input-lg input-group width-330 text-small" input-group></textarea>

    <br/>
    <button class="btn">Submit</button>
</form>

<?php $buffer->collect("content"); ?>

<?php echo $buffer; ?>
