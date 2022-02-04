<?php if (isset($errors) && !empty($errors)) {?>
    <div class="alert-box alert with-icon">
        <p>We're sorry, the following error(s) occured:</p>
        <ul>
            <?php foreach ($errors as $error) {?>
                <li>
                    <?= htmlspecialchars($error) ?>
                </li>
            <?php }?>
        </ul>
    </div>
<?php }?>
