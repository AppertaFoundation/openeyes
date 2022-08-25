<?php

namespace OE\factories\models\traits;

trait HasFactory
{
    public static function factoryName()
    {
        return static::class . 'Factory';
    }

    /**
     * This ensures that the factories/models path is imported
     * for instantiation in non-namespaced modules
     */
    public static function importNonNamespacedFactories()
    {
        if (!preg_match('/OEModule/', static::class)) {
            // not a namespaced model class
            $rc = new \ReflectionClass(static::class);
            $class_path = dirname($rc->getFileName());
            $path_segments = explode(DIRECTORY_SEPARATOR, $class_path);
            // get the module name from the file path (assumes models directory)
            $module_name = $path_segments[count($path_segments) - 2];
            \Yii::import("{$module_name}.factories.models.*");
        }
    }
}
