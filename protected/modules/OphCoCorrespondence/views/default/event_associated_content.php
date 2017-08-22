<div class="row field-row">
    <div  class="large-2 column">
        <label>Appended events:</label>
    </div>
    <div class="large-10 column end">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Event Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($associated_content as $ac){
                    $method = $ac->initMethod->method;
                ?>
                <tr>
                    <td><?= $ac->display_title ?></td>
                    <td><?= $api->{$method}() ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
