<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Frank Nägler <typo3@naegler.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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

if (!class_exists('tslib_pibase')) require_once(PATH_tslib . 'class.tslib_pibase.php');


/**
 * Plugin 'social bookmark icons' for the 'timtab_sociable' extension.
 *
 * @author	Frank Nägler <typo3@naegler.net>
 * @package	TYPO3
 * @subpackage	tx_timtabsociable
 */
class tx_timtabsociable_pi1 extends tslib_pibase {
	var $prefixId = 'tx_timtabsociable_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_timtabsociable_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'timtab_sociable';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->getVars = t3lib_div::_GET();
		$this->tt_newsVars = $this->getVars['tx_ttnews'];
		$this->postID = (isset($this->tt_newsVars['tt_news'])) ? intval($this->tt_newsVars['tt_news']) : (null);
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');

		
		$content='';
		$tagline='';
		
		if (isset($this->conf['tagline.'])) $tagline = $this->local_cObj->stdWrap($this->pi_getLL('tagline'),$this->conf['tagline.']);
		$content .= $tagline;

		$activeServices = t3lib_div::trimExplode(',', $this->conf['activeServices'], true);
		for ($i=0;$i<count($activeServices);$i++) {
			$service = $activeServices[$i];
			$target = (strlen($this->conf['services.'][$service.'.']['target'])) ? (' target="'.$this->conf['services.'][$service.'.']['target'].'"') : ('');
			$icon = '<a href="'.$this->conf['services.'][$service.'.']['url'].'" title="'.$this->conf['services.'][$service.'.']['title'].'"'.$target.'>'.$this->local_cObj->IMAGE($this->conf['services.'][$service.'.']['icon.']).'</a>';
			$content .= (isset($this->conf['services.'][$service.'.']['wrap'])) ? ($this->local_cObj->stdWrap($icon, $this->conf['services.'][$service.'.'])) : ($icon);
		} 

       $content = $this->prepareOutput($content);
		if (isset($this->conf['outerWrap'])) $content = $this->local_cObj->stdWrap($content,$this->conf['outerWrap.']);
		return $this->pi_wrapInBaseClass($content);
	}

	/**
    * prepare the output for rendering, replace the placeholder with content
    *
    * @param	string	$cont: content with placeholder
    * @return   string  parsed content with replaces placeholder
    */
	function prepareOutput($cont) {
		if ($this->postID) {
		        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	        	    'title',
		            'tt_news',
	        	    'uid=' . $this->postID . $this->local_cObj->enableFields('tt_news')
		        );
		        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$title = $row['title'];
		} else {
			$where = 'uid='.$GLOBALS['TSFE']->id;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', $where);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if (is_array($row) && count($row) > 0) {
				$this->local_cObj->start($row, 'pages');
				$title = $this->local_cObj->cObjGetSingle($this->conf['alternativeTitle'],$this->conf['alternativeTitle.']);
			} 
		}
		$url = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
		if ((!isset($url)) || ($url == ''))
			$url = substr(t3lib_div::getIndpEnv('TYPO3_SITE_URL'), 0, strlen(t3lib_div::getIndpEnv('TYPO3_SITE_URL'))-1) . t3lib_div::getIndpEnv('REQUEST_URI');

        $cont = str_replace('%URL%', urlencode($url), $cont);
        $cont = str_replace('%URL_DOUBLE_ENCODED%', urlencode(urlencode($url)), $cont);
        $cont = str_replace('%TITLE%', urlencode($title), $cont);
        return $cont;
    }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_sociable/pi1/class.tx_timtabsociable_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_sociable/pi1/class.tx_timtabsociable_pi1.php']);
}

?>
