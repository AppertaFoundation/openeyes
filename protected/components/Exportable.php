<?php


interface Exportable
{
    public function getExportUrl();
    public function export($file_path, $ws_type = 'SOAP', $client_obj = null);
}