<table class="last-left">
  <colgroup>
    <col>
    <col class="cols-2">
    <col class="cols-2">
  </colgroup>
    <thead>
    <tr>
      <th>Disorder</th>
      <th>Status</th>
      <th>Main Cause</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($disorder_section->disorders as $disorder) {
        $affected = $element->hasCviDisorderForSide($disorder, $side);
        $main_cause = $element->isCviDisorderMainCauseForSide($disorder, $side);
        ?>
            <tr>
              <td><?php echo $disorder->name; ?></td>
              <td><?php echo ($affected) ? 'Yes' : 'No'; ?></td>
              <td><?php echo ($main_cause) ? 'Yes' : 'No';?></td>
            </tr>
    <?php } ?>
    </tbody>
</table>