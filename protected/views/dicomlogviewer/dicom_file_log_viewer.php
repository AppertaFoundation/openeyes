<div class="admin box">
    <h1 class="badge admin">DICOM Log Viewer</h1>
    <form id="dicom_file_watcher">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th>ID</th>
                <th>Date Time</th>
                <th>File Name</th>
                <th>Status</th>
                <th>Process Name</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td>

                    <?php
                    foreach ($data as $key => $val) {
                        echo '<tr data-id='.$val['id'].' filename='.basename($val['dicom_file_id']).' status='.$val['status'].' >
                                    <td id="id">'.$val['id'].'</td>
                                     <td id="event_date_time">'.$val['event_date_time'].'</td>
                                     <td id="filename"><a>'.basename($val['dicom_file_id']).'</a></td>
                                     <td id="status">'.$val['status'].'</td>
                                     <td id="process_name">'.$val['process_name'].'</td>
                                  </tr>';
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
