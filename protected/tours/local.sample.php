<?php
/**
 * This is file should be saved as local.php. It follows exactly the same pattern as
 * common.php to define tours specific to an installation. This allows additional
 * self directed training/tours to be defined for OpenEyes.
 */
return array(
    array(
        'name' => 'Local Tour Sample',
        // this id must be globally unique ... as suggested here, a prefix of local
        // will ensure this.
        'id' => 'local-homepage',
        'url_pattern' => '/^\/{0,1}$/',
        // position can be negative to ensure it appears before the common tours
        'position' => -5,
        'auto' => true,
        'steps' => array(
            array(
                'orphan' => true,
                'title' => 'Local Tour',
                'content' => 'A local tour for this instance. Should be first!'
            )
        )
    )
);
