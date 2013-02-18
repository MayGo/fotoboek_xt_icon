<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 20012 Maigo Erit <maigo.erit@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * XCLASS for the 'goof_fotoboek' extension.
 *
 * 'goof_fotoboek' is (c) Arco <arco@appeltaart.mine.nu>
 *
 * @author Maigo Erit <maigo.erit@gmail.com>
 */

class ux_tx_gooffotoboek_pi1 extends tx_gooffotoboek_pi1 {

	function show_thumb_icon($imgfile, $file_icon) {

		$image = $this -> conf['image.'];

		$image['file'] = $file_icon;

		list($width, $height, $type, $attr) = @getimagesize($image['file']);
		$width /= 10;
		$height /= 10;
		//debug($image['file']);

		if ($width == $height) {
			$this -> orientation = 'square';
		} elseif ($width > $height) {
			$this -> orientation = 'landscape';
		} else {
			$this -> orientation = 'portret';
		}

		$image['file.']['params'] = $this -> conf['thumbIMoptions'];

		$image['file.']['maxW'] = $this -> conf['thumb_maxw'] . 'm';
		$image['file.']['maxH'] = $this -> conf['thumb_maxh'] . 'm';
		$image['altText'] = $this -> pi_getLL('thumb_alt');
		if ($option != 'nolink') {
			$image['altText'] = $this -> pi_getLL('thumb_link_alt');
			$src .= $this -> urlvars['srcdir'] ? $this -> urlvars['srcdir'] . '/' : '';
			$image['linkWrap'] = '<a href="' . $this -> htmldir . '/' . $src . $imgfile . '"' . ' target="picturefull">|</a>';
		}
		return $this -> cObj -> IMAGE($image);
	}

	#/showthumbs

	/**
	 * No return, but filling internal var with template content
	 */
	function showThumbs() {
		$fileExtConf = $this -> conf['extensions.'];

		##@@
		if (is_array($this -> files)) {
			$start = ($this -> urlvars['fid']) ? $this -> urlvars['fid'] : 0;
			$thumbsPerPage = $this -> conf['thumb_per_row'] * $this -> conf['thumb_rows'];
			//directories with less then the maximum thumbs per page should start with the first

			$start = $this -> thumbstart;
			$thumbsPerPage = $this -> thumbsPerPage;

			$this -> thumbnailstxt = '';

			//replace ###orientation### with the thumbnail orientation.
			$rowWrap = explode('|', stripslashes($this -> conf['thumb_row_wrap']));
			for ($i = 0; $i < $this -> conf['thumb_rows']; $i++) {
				$rowtxt = '';
				$j = 0;
				for ($this -> fid = ($start + ($i * $this -> conf['thumb_per_row'])); $this -> fid < ($start + ($i * $this -> conf['thumb_per_row']) + $this -> conf['thumb_per_row']); $this -> fid++) {
					$t = '';
					if ($this -> fid < $this -> filecount) {
						//check if there is an override icon
						$file_ext = strtolower(substr(strrchr($this -> files[$this -> fid], '.'), 1));
						if (array_key_exists($file_ext . ".", $fileExtConf)) {
							if ($file_icon = $fileExtConf[$file_ext . "."]['icon']) {
								$t = $this -> show_thumb_icon($this -> files[$this -> fid], $file_icon);
							}
						}
						if ($t == '')
							$t = $this -> show_thumb($this -> files[$this -> fid]);
					} else {
						#       $t = '&nbsp;'; //some cells disapear when nothing's in it.
						$t = '';
						//some cells disapear when nothing's in it.
						$this -> orientation = 'empty';
					}
					if ($this -> conf['useThumbnailTemplate'] == 0) {
						$thumbWrap = explode('|', stripslashes(preg_replace(array('/###orientation###/i', '/###thumbid###/i'), array($this -> orientation, 'r' . $i . 'c' . $j), $this -> conf['thumb_wrap'])));
						$rowtxt .= $thumbWrap[0] . $t;

						if ($this -> conf['thumb_filenames']) {
							$title = htmlspecialchars($this -> files[$this -> fid]);
							if ($this -> conf['replaceTitleUnderscores']) {
								$title = str_replace('_', ' ', $title);
							}
							$rowtxt .= $title;
						}
						$rowtxt .= $thumbWrap[1] . $this -> crlf;
					} else {
						$file = $this -> startdir . '/' . $this -> urlvars['srcdir'] . '/' . $this -> files[$this -> fid];
						$comment = $this -> loadComment($file, $language = '', $nodefault = 0);
						$globalMarkerArray['###ORIENTATION###'] = $this -> orientation;
						$globalMarkerArray['###THUMBID###'] = 'r' . $i . 'c' . $j;
						$globalMarkerArray['###THUMBNAIL_IMAGE###'] = $t;
						$globalMarkerArray['###THUMBNAIL_COMMMENT_HEADER###'] = $comment['header'];
						$globalMarkerArray['###THUMBNAIL_FILENAME###'] = htmlspecialchars($this -> files[$this -> fid]);
						$globalMarkerArray['###THUMBNAIL_FILESIZE###'] = filesize($file);
						$rowtxt .= $this -> cObj -> substituteMarkerArray($this -> local_cObj -> getSubpart($this -> totalTemplate, '###THUMBNAIL###'), $globalMarkerArray);

					}
					$j++;
				}

				$this -> thumbnailstxt .= $rowWrap[0] . $rowtxt . $rowWrap[1] . $this -> crlf;
			}
		}
	}

}

#End of class

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xtended_fotoboek/class.ux_tx_gooffotoboek_pi1.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xtended_fotoboek/class.ux_tx_gooffotoboek_pi1.php']);
}
?>