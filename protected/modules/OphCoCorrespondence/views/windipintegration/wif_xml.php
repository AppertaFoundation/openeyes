<?php echo '<?xml version="1.0" encoding="utf-8" ?>' ?>

<fileparameters>
    <?php /* message definition revision number to allow backward compatibility with future messages */ ?>
    <revision number="1.2"/>
    <primarylink>
        <?php /* the unique reference field is the 3rd party system's internal unique reference, any additional information like names and dates can be done via a
                lookup setup within WinDIP or added using the additional indexes section below */ ?>
        <uniquereference type="I"><?=$event_id;?></uniquereference>
        <?php /* optional, the linking system's type id that can link to a WinDIP information type
        allowing a faster capture process and the correct type of document
        to be acquired, if it is not passed then WinDIP will use the default type setup within options */ ?>
        <typeid><?=$windip_type_id;?></typeid>
        <?php /* optional, used to tell WinDIP Enterprise whether any previous
        documents captured with the same unique reference are marked as deleted or if all
        previous documents should be kept (defaults to removing previous documents)*/ ?>
        <keeppriordocuments><?=$keep_prior_documents;?></keeppriordocuments>
        <?php /* optional, used to tell WinDIP Enterprise when automatically entering
        the document into a workflow as a result of automated file capture,
        whether or not the workflow should be flagged as high priority.
        1 = high priority, 0 = normal priority.
        */ ?>
        <workflowimportance><?=$workflow_importance;?></workflowimportance>
    </primarylink>

    <?php /* optional, this element contains the URL of the document to be archive into
    WinDIP Enterprise, if the <filepath> element only the WIF file is
    required :: <filepath>\\server\share\document.txt</filepath> */ ?>
    <filepath><?=$file_path;?></filepath>
    <additionalindexes count="<?=count($additional_indexes)?>">
        <?php foreach ($additional_indexes as $i => $index) {?>
        <index_<?=$i+1?>>
            <id><?=$index['id']?></id>
            <value><?=$index['value']?></value>
        </index_<?=$i+1?>>
        <?php } ?>
    </additionalindexes>
    <?php /* optional, used to tell WinDIP Enterprise the content of the
    document. Each word must be separated with a "|", and the value must be stored in base64 format */ ?>
    <Content><?php echo !empty($content) ? base64_encode($content) : '';?></Content>
    <?php /* optional, used to tell WinDIP Enterprise additional instructions */ ?>
    <?php /* not used at the moment
    <instructions>

        <instruction type="bordercolour">

            <!-- range is used to specify the page(s) to colour. Multiple

            pages must be separated

            with ";". Consecutive pages can be specified (e.g. 1-5) -->

            <parameter type="range">6;9</parameter>

            <!-- colour is the Hex colour value to be used when colouring the

            pages specified

            (e.g. FF0000 = Red) -->

            <parameter type="colour">FF0000</parameter>

        </instruction>

        <instruction type="bordercolour">

            <?php /* range is used to specify the page(s) to colour. Multiple

            pages must be separated

            with ";". Consecutive pages can be specified (e.g. 1-5) -->

            <parameter type="range">1-4;8;10-15</parameter>

           <!-- colour is the Hex colour value to be used when colouring the

            pages specified

            (e.g. 00FF00 = Green) -->

            <parameter type="colour">00FF00</parameter>

        </instruction>
    </instructions>
    */ ?>
</fileparameters>