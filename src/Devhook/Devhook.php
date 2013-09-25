<?php namespace Devhook;



class Devhook
{
	//--------------------------------------------------------------------------

	// protected static $componentLocations = array();
	// protected static $fieldTypes = array();

	//--------------------------------------------------------------------------

	// public static function addComLocation($path)
	// {
	// 	if (empty(static::$componentLocations[$path])) {
	// 		static::$componentLocations[$path] = $path;
	// 	}
	// }

	//--------------------------------------------------------------------------

	public static function scanModels()
	{
		$components = static::getComponents();
		$models     = array();

		foreach ($components as $name => $paths) {
			$modelClass = ucfirst($name);

			foreach ($paths as $comPath) {
				$modelFile  = $comPath . $modelClass . '.php';

				if (file_exists($modelFile)) {
					$models[$modelClass] = $modelFile;
					break;
				}
			}
		}

		return $models;
	}

	//--------------------------------------------------------------------------

	public static function getComponents()
	{
		static $components;

		if ($components === null) {
			$components = array();
			$paths = array(
				base_path() . '/components/',
				realpath(__DIR__ . '/../components') . '/',
			);
			foreach ($paths as $dir) {
				$files = scandir($dir);
				foreach ($files as $f) {
					if ($f{0} != '.' && is_dir($dir . $f)) {
						if (empty($components[$f])) {
							$components[$f] = array();
						}
						$components[$f][] = $dir . $f . '/';
					}
				}
			}
		}

		return $components;
	}

	//--------------------------------------------------------------------------

	public static function getClassByKey($modelKey)
	{
		return '\\' . ucfirst(str_replace('.', '\\', $modelKey));
	}

	//--------------------------------------------------------------------------

	public static function makeObjectByKey($modelKey)
	{
		$modelClass = static::getClassByKey($modelKey);
		return new $modelClass;
	}

	//--------------------------------------------------------------------------

	// public static function registerFieldType($type, $className)
	// {
	// 	$className::boot();
	// 	static::$fieldTypes[$type] = $className;
	// }

	//--------------------------------------------------------------------------

	// public static function fieldClass($type)
	// {
	// 	$fieldSettings = array();
	// 	if (is_array($type)) {
	// 		$fieldSettings = $type;
	// 		$type = $fieldSettings['field'];
	// 	}

	// 	$class = isset(static::$fieldTypes[$type]) ? static::$fieldTypes[$type] : false;

	// 	if ($class) {
	// 		$class::fieldSettings($fieldSettings);
	// 		return $class;
	// 	}

	// 	return false;
	// }

	//--------------------------------------------------------------------------

}