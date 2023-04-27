<?php

return array(
    'import' => array(
        'application.modules.Genetics.models.*',
        'application.modules.Genetics.components.*',
    ),
    'params' => array(
        'menu_bar_items' => array(
            'pedigrees' => array(
                'title' => 'Genetics',
                'uri' => 'Genetics/default/index',
                'restricted' => array('Genetics Admin', 'Genetics Clinical', 'Genetics Laboratory Technician', 'Genetics User'),
            ),
        ),
        'module_partials' => array(
            'patient_summary_column1' => array(
                'Genetics' => array(
                    '_patient_genetics',
                ),
            ),
        ),
        'admin_structure' => array(
        ),
        'admin_menu' => array(
        ),
        'additional_rulesets' => array(
            array(
                'namespace' => 'Genetics',
                'class' => 'Genetics_AuthRules'
            ),
        ),

        /**
         * Returned result must be in JSON format
         *
         * Minimum expected structure: {valid: false, 'message':''}
         */
        'external_gene_validation' => function ($variant) {

            $url = "https://mutalyzer.nl/json/checkSyntax?variant=" . $variant;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // The maximum number of seconds to allow cURL functions to execute.
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); //timeout in seconds

            // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            $result = curl_exec($ch);

            $message = '';
            if (curl_error($ch)) {
                $message = curl_error($ch);
            }

            curl_close($ch);

            return $result === false ? json_encode(['valid' => 'failed', 'message' => $message ]) : $result;
        },
    ),
);
