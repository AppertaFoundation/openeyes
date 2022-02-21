<?php if (isset($errors) && !empty($errors)) {?>
    <div class="alert-box alert with-icon">
        <p>Please fix the following input errors:</p>
        <ul>
            <?php foreach ($errors as $field => $errs) {?>
                <?php foreach ($errs as $err) {?>
                    <li>
                        <?= htmlspecialchars(is_array($err) ? implode(" ", $err) : $err) ?>
                    </li>
                <?php }?>
            <?php }?>
        </ul>
    </div>
<?php }?>
