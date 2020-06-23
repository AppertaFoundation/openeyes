<?php $this->renderPartial('//base/_messages'); ?>
<style>
    ul.add-options.js-search-results{
        max-height: 100px;
        overflow-y: auto;
    }
    .js-search-results li:hover{
        background-color: white;
        color: #141e2b;
        cursor: pointer;
    }
</style>
<div class="cols-8">
    <form id="admin_manage_lasers" action="/OphTrLaser/admin/processlaserprocedures" method="POST">
        <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="laser-procedure-list cols-10 standard">
            <thead>
                <th>Laser Procedures</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($laser_procs as $index => $laser_proc) {?>
                    <tr>
                        <td>
                            <input 
                                style="width:100%" 
                                name="laser_proc[<?=$index?>][term]" 
                                class="procedures-search-autocomplete"
                                type="text" 
                                value="<?=$laser_proc['term']?>"
                                id="laser_proc_<?=$index?>"
                                autoComplete="off"
                                data-index="<?=$index?>"
                                required
                            >
                            <input type="hidden" name="laser_proc[<?=$index?>][id]" value="<?=$laser_proc['id']?>">
                            <input type="hidden" name="laser_proc[<?=$index?>][proc_id]" value="<?=$laser_proc['procedure_id']?>">
                            <input type="hidden" name="laser_proc[<?=$index?>][mode]" value="original">
                            <ul id="laser_proc_<?=$index?>_list" class="add-options js-search-results"></ul>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="delete">delete</a>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
        <div>
            <button class="button large" type="button" id="add_new">Add</button>
            <button class="generic-admin-save button large" name="admin-save" type="submit" id="et_admin-save">Save</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // maintain an index key for easy access
        for(var i = 0; i < all_procs.length; i++){
            all_procs[i]['index'] = i;
        }
    
        // callback for input field onblur
        // put the original text back once the input field losses focus
        function setToOriginalText(){
            var term = laser_procs[$(this).data('index')] ? laser_procs[$(this).data('index')]['term'] : '';
            var pro_id = laser_procs[$(this).data('index')] ? laser_procs[$(this).data('index')]['pro_id'] : '';
            $(this).val(term);
            $(this).siblings('input[name$="[proc_id]"]').val(pro_id);
        }

        // mute the brand new input field
        // set mode to delete for backend use
        function deleteRecord(e){
            e.preventDefault();
            $(this).closest('tr').css('opacity', '.5');
            if($(this).closest('tr').find('input[name$="[mode]"]').val() === 'create'){
                $(this).closest('tr').find('input').attr('disabled', true);
                return;
            }
            $(this).closest('tr').find('input[name$="[term]"]').attr('disabled', true);
            $(this).closest('tr').find('input[name$="[mode]"]').val('delete');
        }

        // close the search result list
        function closeSearchResults(){
            var input = $('#' + this.dataset.target).siblings('input.procedures-search-autocomplete');
            var term = laser_procs[input.data('index')] ? laser_procs[input.data('index')]['term'] :'';
            $('#' + this.dataset.target).html('');
            $('#' + this.dataset.target).siblings('input.procedures-search-autocomplete').val(term);
        }

        function selectProc(e){
            e.stopPropagation();
            document.getElementById(this.dataset.target).value = this.innerText;
            $('#' + this.dataset.target).siblings('input[name$="[proc_id]"]').val(this.dataset.id);
            var proc = laser_procs[$('#' + this.dataset.target).data('index')];
    
            if(laser_procs[$('#' + this.dataset.target).data('index')]){
                // edit existing procedure list
                $('#' + this.dataset.target).siblings('input[name$="[mode]"]').val('edit')
                proc['index'] = this.dataset.itemIndex;
                all_procs[this.dataset.itemIndex]['id'] = proc['id'];
                laser_procs.splice($('#' + this.dataset.target).data('index'), 1, all_procs[this.dataset.itemIndex])
                all_procs.splice(this.dataset.itemIndex, 1, proc);
            } else {
                // add new procedure to the list
                laser_procs.push(all_procs[this.dataset.itemIndex]);
                all_procs.splice(this.dataset.itemIndex, 1);
            }
            $(this).parent().html('');
        }
    
        // after inputing 3 characters, the matched procedures will be
        // displayed in the search result list
        function autoComplete(e){
            var input_id = $(this).attr('id')
            var input_val = $(this).val().trim();
            var ul_id = input_id + '_list';

            // making sure the keyup event only accept alphabets and backspace input
            if(e.key.length != 1){
                if(e.key.toLowerCase() != 'backspace'){
                    return;
                }
            }
            $('ul.add-options.js-search-results').html('');
            if(!$(this).val().trim() || $(this).val().trim().length < 3){
                return;
            }

            // searching in all procedures list
            const result = all_procs.filter(function(item, i){
                return item.term.toLowerCase().includes(input_val.toLowerCase())
            });
            if(result.length){
                // found some matched procedures
                var close = document.createElement('div');
                var icon = document.createElement('i');
                icon.classList.add(...['oe-i', 'remove-circle', 'small']);
                close.appendChild(icon);
                close.classList.add(...['close-icon-btn', 'close-search-result']);
                close.style.float = 'right';
                close.style.cursor = 'pointer';
                close.dataset.target = ul_id;
                $(close).off('click').on('click', closeSearchResults);
                $('#' + ul_id).append(close);
                for(var i in result){
                    var li = document.createElement('li');
                    li.innerText = result[i]['term'];
                    li.dataset.id = result[i]['procedure_id'];
                    li.dataset.itemIndex = result[i]['index'];
                    li.dataset.target = input_id;
                    li.style.padding = '5px';
                    li.class = ul_id + '_item';
                    $(li).off('click').on('click', selectProc)
                    $('#' + ul_id).append(li);
                }
            } else {
                // message to indicate if the procedure already exists in the list or not found
                var div = document.createElement('div');
                var listed = laser_procs.filter(function(proc){
                    return proc['term'].toLowerCase().includes(input_val.toLowerCase())
                });
                var msg_box_style = listed.length > 0 ? ['alert-box', 'alert'] : ['alert-box', 'warning'];
                var msg = listed.length > 0 ? 'Possible match(es) found from the listed procedures: ' + listed.map(item=>item.term).join(', ') : 'No procedure matched';
                div.classList.add(...msg_box_style)
                div.innerText = msg;
                $('#' + ul_id).append(div);
            }
        }
        // put attributes on new created element
        function setNewRowAttr(newRow){
            var rowCount = $('table.laser-procedure-list tbody tr').length;
            $(newRow).find('input.procedures-search-autocomplete')
                .attr('name', 'laser_proc[' + rowCount + '][term]')
                .attr('id', 'laser_proc_' + rowCount)
                .attr('autocomplete', 'off')
                .attr('data-index', rowCount)
                .attr('required', true)
                .addClass('procedures-search-autocomplete')
                .css('width', '100%');
            $(newRow).find('input[name="proc_id"]')
                .attr('name', 'laser_proc[' + rowCount + '][proc_id]');
            $(newRow).find('input[name="mode"]')
                .attr('name', 'laser_proc[' + rowCount + '][mode]')
                .val('create');
            $(newRow).find('ul')
                .attr('id', 'laser_proc_' + rowCount + '_list')
                .addClass('add-options')
                .addClass('js-search-results')
                .css('max-height', '100px')
                .css('overflow-y', 'auto');
        }
        // create new element with basic setup
        function addNewRow(){
            var newRow = document.createElement('tr');
            var newInputTd = document.createElement('td');
            var newActionTd = document.createElement('td');
            var newTermInput = document.createElement('input');
            newTermInput.classList.add('procedures-search-autocomplete');
            var newProcIdInput = document.createElement('input');
            newProcIdInput.type = 'hidden';
            newProcIdInput.name = 'proc_id';
            var newModeInput = document.createElement('input');
            newModeInput.type = 'hidden';
            newModeInput.name = 'mode';
            var newUl = document.createElement('ul');
            var newAction = '<a href="javascript:void(0)" class="delete">delete</a>';
            newInputTd.appendChild(newTermInput);
            newInputTd.appendChild(newProcIdInput);
            newInputTd.appendChild(newModeInput);
            newInputTd.appendChild(newUl);
            newActionTd.innerHTML = newAction;
            newRow.appendChild(newInputTd);
            newRow.appendChild(newActionTd);
            setNewRowAttr(newRow)
            $('table.laser-procedure-list tbody').append(newRow);
        }

        $(document).off('keyup').on('keyup', 'input.procedures-search-autocomplete', autoComplete);
        $(document).off('click').on('click', 'a.delete', deleteRecord);
        $(document).off('blur').on('blur', 'input.procedures-search-autocomplete', setToOriginalText);
    
        $('#add_new').off('click').on('click', addNewRow);
    })
</script>