<?php

    $empty_pedigree_save = false;
    // user removed all pedigrees then clicked on the Save button
if (isset($_POST['GeneticsPatient']['pedigrees']) && empty($_POST['GeneticsPatient']['pedigrees'])) {
    $empty_pedigree_save = true;
}

?>
<div class="flex-layout cols-full <?php echo (!$genetics_patient || !$genetics_patient->pedigrees || $empty_pedigree_save) ? '' : ' hidden'; ?> ">
    <div class="cols-7">&nbsp;</div>
    <div class="cols-5">
        <input type="checkbox" id="no_pedigree" name="no_pedigree" />
        <label for="no_pedigree" style="display: inline">Automatically generate pedigree</label>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("#no_pedigree").click(function(){
            var checked = $(this).prop("checked");
            if(checked)
            {
                $("#div_GeneticsPatient_Pedigree .MultiSelectList .multi-select-remove").trigger("click");
                $('#GeneticsPatient_pedigrees').prop('disabled', true).trigger("chosen:updated");
            }
            else
            {
                $('#GeneticsPatient_pedigrees').prop('disabled', false).trigger("chosen:updated");
            }
        });

        var confirmed = false;

        $("#generic-admin-form").submit(function(){

            if(confirmed === true)
            {
                return true;
            }

            if($("#no_pedigree").prop("checked"))
            {
                var confirm = new OpenEyes.UI.Dialog.Confirm({
                    content: 'Are you sure there is no pedigree?'
                });


                confirm.content.on("click", '.ok', function(){
                    confirmed = true;
                    $("#generic-admin-form").submit();
                });

                confirm.open();
            }
            else
            {
                return true;
            }

            return false;
        });
    });
</script>
