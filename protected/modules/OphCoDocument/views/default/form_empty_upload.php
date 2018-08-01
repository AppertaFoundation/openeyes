<div class="upload-box">
    <label for="Document_<?php echo $index; ?>_id" id="upload_box" class="upload-label"
           ondrop="drop(event)" ondragover="allowDrop(event)">
        <i class="oe-i download medium"></i>
        <br> Click to select file or DROP here</label>
    <input autocomplete="off" type="file" name="Document[<?php echo $index; ?>_id]"
           id="Document_<?php echo $index; ?>_id" style="display:none;">
</div>
<style>


  #ophco-document-viewer{
    border:1px solid #eaddaf;
    margin-top:20px;
  }
  #ophco-document-viewer .tabs{
    display: block;
    width:100%;
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    border-radius: 0px;
    background:#eaddaf;
  }

  #ophco-document-viewer .tabs .ui-state-default
  #ophco-document-viewer .tabs .ui-state-default a
  {
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    border-radius: 0px;
  }

  #ophco-document-viewer .tabs .ui-tabs-active a{
    background: #3665ff;
    color: #fff;
  }
  .ophco-image-container{
    position:relative;
  }
  .ophco-image-container:hover img{
    opacity: 0.5;
  }

  .ophco-image-container:hover .image-del-icon{
    display:block;
  }
  .ophco-image-container .image-del-icon{
    display:none;
    position:absolute;
    z-index: 2;
    right: 0px;
    top: 0px;
    font-size: 30px;
    cursor: pointer;
    background: #FFF;
    padding:5px 15px;
    color:#FF0000;
  }

  .upload-box{
    border: 1px solid #f5f5f5;
    width: 219px;
  }

  .upload-label{
    cursor: pointer;
    display: block;
    font-size: 20px;
    height: 100%;
    position: relative;
    text-align: center;
    top: 0;
    padding-top: 10px;
    width: 100%;
  }

  img {
    max-width: 100%;
  }

  </style>