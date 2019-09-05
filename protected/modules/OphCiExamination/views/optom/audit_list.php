<div class="cols-12 column">
  <div class="box generic">
      <?php
      if (count($data) == 0) { ?>
        <div class="alert-box">
          No audit logs match the search criteria.
        </div>
        <?php } else { ?>
        <div class="pagination"></div>
        <table class="standard audit-logs">
          <thead>
          <tr>
            <th>Created date</th>
            <th>User</th>
            <th>Invoice Status</th>
            <th>Comment</th>
          </tr>
          </thead>
          <tbody id="auditListData">
            <?php foreach ($data as $i => $log) {
                $this->renderPartial('/optom/audit_list_row', array('i' => $i, 'log' => $log));
            } ?>
          </tbody>
        </table>
        <div class="pagination last"></div>
        <?php } ?>
  </div>
</div>
