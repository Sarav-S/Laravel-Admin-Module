<?php

namespace Sarav\Admin\Console\Traits;

trait FolderTrait
{
	public function processFolderPath($name, $folder)
	{
		$processedFolder = $this->getNamespaceFolder();
        $name = str_replace($processedFolder['namespace'], '', $name);
        $path = $processedFolder['path']. rtrim($this->getFolderName(), '/').'/';

        $name = explode('\\', $name);
        $name = end($name);

        return $path.$folder.$name.'.php';
	}

	public function getNameForFolderRendering()
	{
		$name = explode('/', $this->getNameInput());

		$lastName = str_plural(end($name));

		array_pop($name);

		$name[] = $lastName;

		return strtolower(implode('/', $name));
	}
}
