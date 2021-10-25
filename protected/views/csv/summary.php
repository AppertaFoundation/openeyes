<main class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">Import Summary</div>
    </div>
    <div class="oe-full-content">
        <div class="errorSummary">
        </div>
        <?php

        if (!empty($table)) : ?>
            <div style="overflow: auto">
                <table class="standard highlight-rows">
                    <tr>
                        <?php foreach (array_keys($table[0]) as $header) : ?>
                            <th>
                                <?php echo $header; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($table as $row) : ?>
                        <tr>
                            <?php foreach ($row as $column) : ?>
                                <td>
                                    <?php echo $column ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <?= CHtml::link('Back to upload page', '/csv/upload?context=' . $context, ['class' => 'button large',]); ?>
            </div>
        <?php endif; ?>
    </div>
</main>