
<tr id="conflicts">
  <p>Conflicts could not be found due to the following errors:</p>
  <ul>
    <?php foreach ($errors as $name => $attribute) : ?>
        <?php foreach ($attribute as $message) : ?>
        <li><?php echo Patient::model()->getAttributeLabel($name) . ": $message"; ?></li>
        <?php endforeach;
    endforeach; ?>
  </ul>
</tr>