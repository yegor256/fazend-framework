<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Compresses many PNG/GIF files into one big holder
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_SqueezePNG {

	/**
	* Local link to Zend_View
	*
	* @var Zend_View
	*/
	private $_view;

	private $_file;

	/**
	* Get path of temp PNG holder
	*
	* @return string
	*/
	public function getImagePath() {
		$url = $this->getView()->url(array('id'=>'global'), 'squeeze', true);
		return sys_get_temp_dir().'/fazend-'.md5($url).'.png';
	}           

	/**
	* Get map path
	*
	* @return string
	*/
	public function getMapPath() {
		return $this->getImagePath().'.data';
	}           

	/**
	* Load map
	*
	* @return array
	*/
	public function loadMap() {
		$file = $this->getMapPath();

		if (!file_exists($file))
			return array();

		$map = @unserialize(file_get_contents($file));
		if (!is_array($map))
			return array();

		// checksum of the image file	
		if ($map['md5'] != md5_file($this->getImagePath()))	
			return array();

//		return array();
		return $map;	
	}           

	/**
	* Save map
	*
	* @param array Map
	* @param string PNG image (all images together)
	* @return void
	*/
	public function saveMap($map, $png) {
		$file = $this->getMapPath();
		$pngFile = $this->getImagePath();

		file_put_contents($pngFile, $png);

		$map['md5'] = md5_file($pngFile);

		file_put_contents($file, serialize($map));
	}
		
	/**
	* Save view locally
	*
	* @return void
	*/
	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}           

	/**
	* Get view saved locally
	*
	* @return Zend_View
	*/
	public function getView() {
		return $this->_view;
	}

	/**
	* Show the image
	*
	* @return string
	*/
	public function squeezePNG($file) {

		$this->_file = $file;
		return $this;

	}

	/**
	* Render the class
	*
	* @return string HTML
	*/
	public function __toString() {
		return $this->_render();
	}

	/**
	* Start building the image holder from scratch
	*
	* @return string HTML
	*/
	public function startOver() {
		if (file_exists($this->getMapPath()))
			unlink($this->getMapPath());
		if (file_exists($this->getImagePath()))
			unlink($this->getImagePath());
		return $this;	
	}

	/**
	* Show the image
	*
	* @return string
	*/
	private function _render() {

		$url = $this->getView()->url(array('id'=>FaZend_Revision::get().'.png'), 'squeeze', true);
		$file = APPLICATION_PATH . '/views/squeeze/' . $this->_file;

		$map = $this->loadMap();

		if (!is_file($file))
			return 'border: 1px solid red;';

		if (!isset($map['images'][$file]) || (filemtime($file) != $map['images'][$file]['mtime'])) {

			// if it's very fresh - prepare it
			if (!isset($map['images']))
				$map['images'] = array();

			// delete obsolete elements from the map (lost images)
			foreach ($map['images'] as $id=>$img) {
				if (file_exists($id))
					continue;

				unset($map['images'][$id]);
			}

			// add new image
			$png = imagecreatefrompng($file);
			$thisImage = array(
				'md5' => md5_file($file),
			);
			$map['images'][$file] = $thisImage;

			// kill non-existing files
			foreach ($map['images'] as $id=>&$img) {
				if (file_exists($id))
					continue;
				unset($map['images'][$id]);
			}

			foreach ($map['images'] as $id=>&$img) {

				$newSmall = @imagecreatefrompng($id);
				if (!$newSmall) {
					$newSmall = @imagecreatefromgif($id);
					if (!$newSmall) {
						unset($map['images'][$id]);
						continue;
					}
				}	

				if (isset($holder)) {

					// put the image to the bottom if:
					// the width of a new image is LESS OR EQUAL than the space available
					// and the height of new image is less of equal than the space available
					if ((imagesx($newSmall) <= imagesx($holder) - $bottomX) &&
						(imagesy($newSmall) <= imagesy($holder) - $bottomY)) {
						// set new image to the BOTTOM
						$newHolder = imagecreate(max(imagesx($newSmall)+$bottomX,imagesx($holder)), max(imagesy($newSmall)+$bottomY, imagesy($holder)));
						$img['x'] = $bottomX;
						$img['y'] = $bottomY;
					} else {
						// set the new image to the RIGHT
						$newHolder = imagecreate(imagesx($newSmall)+imagesx($holder), max(imagesy($holder), $rightY + imagesy($newSmall)));
						$img['x'] = $rightX;
						$img['y'] = $rightY;
					}	

					// copy everything from the existing holder
					imagecopy($newHolder, $holder, 0, 0, 0, 0, imagesx($holder), imagesy($holder));

					// copy new image to the new holder
					imagecopy($newHolder, $newSmall, $img['x'], $img['y'], 0, 0, imagesx($newSmall), imagesy($newSmall));

					// new image is a new holder!
					$holder = $newHolder;

				} else {
					$img['x'] = 0;
					$img['y'] = 0;
					$holder = $newSmall;
				}	
				
				// get coordinates of right top corner OF the added image
				$rightX = $img['x'] + imagesx($newSmall);
				$rightY = $img['y'];

				// get coordinates of bottom left corner OF the added image
				$bottomX = $img['x'];
				$bottomY = $img['y'] + imagesy($newSmall);

				$img['width'] = imagesx($newSmall);
				$img['height'] = imagesy($newSmall);
				$img['mtime'] = filemtime($id);
			}	

			ob_start();
			imagepng($holder);
			$pngContent = ob_get_contents();
			ob_end_clean();

			$this->saveMap($map, $pngContent);
		}

		return sprintf("background:url({$url}) no-repeat;width:%dpx;height:%dpx;background-position:%dpx %dpx;display:inline-block;",
			$map['images'][$file]['width'],
			$map['images'][$file]['height'],
			-$map['images'][$file]['x'],
			-$map['images'][$file]['y']);

	}

}
