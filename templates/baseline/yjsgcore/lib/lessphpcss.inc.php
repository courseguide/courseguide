<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - JBootstrap                								||
|| # Copyright (C) since 2007  Youjoomla.com. All Rights Reserved.      ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        || 
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   || 
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/

// no direct access 
defined('_JEXEC') or die ('Restricted access');
class lessPhpCss extends lessc{

	protected function tryImport($importPath, $parentBlock, $out){  
		if ($importPath[0] == "function" && $importPath[1] == "url"){
			$importPath = $this->flattenList($importPath[2]);
		}

		$str = $this->coerceString($importPath);
		if ($str === null) return false;

		$url = $this->compileValue($this->lib_e($str));

		$realPath = $this->findImport($url);
		if ($realPath === null) return false;

		if ($this->importDisabled) {
			return array(false, "/* import disabled */");
		}

		$this->addParsedFile($realPath);
		$parser = $this->makeParser($realPath);
		$root = $parser->parse(file_get_contents($realPath));

		// set the parents of all the block props
		foreach ($root->props as $prop) {
			if ($prop[0] == "block") {
				$prop[1]->parent = $parentBlock;
			}
		}

		// copy mixins into scope, set their parents
		// bring blocks from import into current block
		// TODO: need to mark the source parser	these came from this file
		foreach ($root->children as $childName => $child) {
			if (isset($parentBlock->children[$childName])) {
				$parentBlock->children[$childName] = array_merge(
					$parentBlock->children[$childName],
					$child);
			} else {
				$parentBlock->children[$childName] = $child;
			}
		}

		$pi = pathinfo($realPath);
		$dir = $pi["dirname"];

		list($top, $bottom) = $this->sortProps($root->props, true);
		$this->compileImportedProps($top, $parentBlock, $out, $parser, $dir);

		return array(true, $bottom, $parser, $dir);
	}
	/**
	 * Execute lessphp on a .less file or a lessphp cache structure
	 *
	 * The lessphp cache structure contains information about a specific
	 * less file having been parsed. It can be used as a hint for future
	 * calls to determine whether or not a rebuild is required.
	 *
	 * The cache structure contains two important keys that may be used
	 * externally:
	 *
	 * compiled: The final compiled CSS
	 * updated: The time (in seconds) the CSS was last compiled
	 *
	 * The cache structure is a plain-ol' PHP associative array and can
	 * be serialized and unserialized without a hitch.
	 *
	 * @param mixed $in Input
	 * @param bool $force Force rebuild?
	 * @return array lessphp cache structure
	 */
	public function cachedCompile($in, $force = false) {
		// assume no root
		$root = null;

		if (is_string($in)) {
			$root = $in;
		} elseif (is_array($in) and isset($in['root'])) {
			if ($force or ! isset($in['files'])) {
				// If we are forcing a recompile or if for some reason the
				// structure does not contain any file information we should
				// specify the root to trigger a rebuild.
				$root = $in['root'];
			} elseif (isset($in['files']) and is_array($in['files'])) {
				foreach ($in['files'] as $fname => $ftime ) {
					if (!file_exists($fname) or filemtime($fname) > $ftime) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$root = $in['root'];
						break;
					}
				}
			}
		} else {
			// TODO: Throw an exception? We got neither a string nor something
			// that looks like a compatible lessphp cache structure.
			return null;
		}

		if ($root !== null) {
			// If we have a root value which means we should rebuild.
			$out = array();
			$out['root'] = $root;
			$out['updated'] = time();
			return $out;
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}

	}		

}