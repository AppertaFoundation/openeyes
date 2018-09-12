<div class="element-data">
    <table class="large last-left">
        <tbody>
        <tr>
            <td>
                <?= $element->cviStatus->name ?>
            </td>
            <td>
                <?= Helper::convertMySQL2NHS($element->element_date)?>
            </td>
        </tr>
        </tbody>
    </table>
</div>