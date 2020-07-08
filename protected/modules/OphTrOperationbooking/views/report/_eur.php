<table class="standard">
   <thead>
       <tr>
           <?php foreach ($report->getColumns() as $column) {?>
               <th><?php echo $column?></th>
           <?php }?>
       </tr>
   <tbody>
       <?php if (empty($report->eurs)) {?>
           <tr>
               <td colspan="6">
                   No EURs were found with the selected search criteria.
               </td>
           </tr>
       <?php } else {?>
           <?php foreach ($report->eurs as $ts => $operation) {?>
               <tr>
                   <?php foreach ($operation as $item) {?>
                       <td><?php echo $item?></td>
                   <?php }?>
               </tr>
           <?php }?>
       <?php }?>
   </tbody>
</table>