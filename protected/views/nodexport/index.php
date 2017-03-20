<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="box content">
    
    <div class="row">
        <div class="large-12 column">
            <h2>Generate</h2>
        </div>
    </div>
    
    <div class="search-filters theatre-diaries">
        <form method="post" action="/NodExport/Generate" id="nod-export-filter" class="clearfix">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
            <div class="row">
                <div class="large-12 column">
                    <div class="panel">
                        <div class="row">
                           
                            <div class="large-10 column">
                                <div class="search-filters-extra audit-filters clearfix">
                                    <fieldset class="inline highlight">
                                        <label class="inline" for="date_from">From:</label>
                                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                                'name' => 'date_from',
                                                'id' => 'date_from',
                                                'options' => array(
                                                        'showAnim' => 'fold',
                                                                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                                            ),
                                                            'value' => Yii::app()->request->getParam('date_from'),
                                                            'htmlOptions' => array(
                                                                    'class' => 'small fixed-width',
                                                            ),
                                                    ))?>
                                        <label class="inline" for="date_to">To:</label>
                                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                                'name' => 'date_to',
                                                'id' => 'date_to',
                                                'options' => array(
                                                        'showAnim' => 'fold',
                                                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                                ),
                                                'value' => Yii::app()->request->getParam('date_to'),
                                                'htmlOptions' => array(
                                                        'class' => 'small fixed-width',
                                                ),
                                        ))?>

                                    </fieldset>
                                </div>
                            </div> 
                            <div class="large-2 column text-right">
                                <img class="loader hidden" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" alt="loading..." style="margin-right:10px" />
                                <button type="submit" class="secondary long">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="box content">
    
    <div class="row">
        <div class="large-12 column">
            <h2>How to submit the Export to the RCOphth</h2>
        </div>
    </div>
   
    <div class="row">
        <div class="large-12 column">
            <div class="panel">
                <div class="row">
                    <div class="large-10 column">
                        Once NOD export data has been produced it needs to be submitted to the National Ophthalmology Database.<br>
                        RCOphth provide a secure web portal for submission (<a href="https://www.nodaudit.org.uk">https://www.nodaudit.org.uk</a>)
                    </div>
                    <div class="large-2 column"></div>

                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="large-12 column">
            <h2>Registration</h2>
        </div>
    </div>
    <div class="row">
        <div class="large-12 column">
            <div class="panel">
                <div class="row">
                    <div class="large-12 column">
                        <a href="<?php echo Yii::app()->assetManager->createUrl('img/nod-export/register.png');?>">
                            <img src="<?php echo Yii::app()->assetManager->createUrl('img/nod-export/register.png');?>" alt="registration" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="large-12 column">
            <h2>Submission</h2>
        </div>
    </div>
    <div class="row">
      <div class="large-12 column">
          <div class="panel">
              <div class="row">
                  <div class="large-12 column">
                        <a href="<?php echo Yii::app()->assetManager->createUrl('img/nod-export/submission.png');?>">
                            <img src="<?php echo Yii::app()->assetManager->createUrl('/img/nod-export/submission.png');?>" alt="submission" />
                        </a>
                  </div>
              </div>
          </div>
      </div>
    </div>
    
    <div class="row">
        <div class="large-12 column">
            <h2>NOD Login</h2>
        </div>
    </div>
    <div class="row">
        <div class="large-12 column">
          <div class="panel">
              <div class="row">
                  <div class="large-12 column">
                        <img src="<?php echo Yii::app()->assetManager->createUrl('/img/nod-export/NOD_login_screen.png');?>" alt="log in screen" />
                  </div>
              </div>
          </div>
      </div>
    </div>
    
    <div class="row">
        <div class="large-12 column">
            <h2>Select File</h2>
        </div>
    </div>
    <div class="row">
        <div class="large-12 column">
          <div class="panel">
              <div class="row">
                  <div class="large-12 column">
                      <img src="<?php echo Yii::app()->assetManager->createUrl('/img/nod-export/NOD_upload_file.png');?>" alt="Upload file" />
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="row">
        <div class="large-12 column">
            <h2>File Uploaded</h2>
        </div>
    </div>
    <div class="row">
        <div class="large-12 column">
          <div class="panel">
              <div class="row">
                  <div class="large-12 column">
                      <img src="<?php echo Yii::app()->assetManager->createUrl('/img/nod-export/NOD_file_uploaded.png');?>" alt="Uploaded file" />
                  </div>
              </div>
          </div>
      </div>
    </div>
</div>
