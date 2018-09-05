<div class="element-data">
    <table class="large last-left">
        <tbody>
        <tr>
            <td>
                <?= $element->cviStatus->name ?>
            </td>
            <td>
                <?php $date = new DateTime($element->element_date);
                echo $date->format('d M Y'); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>