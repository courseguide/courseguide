<?php
/**
 * sh404SEF support for com_content component.
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007-2011
 * @package     sh404SEF-16
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: com_content.php 2057 2011-07-05 16:27:31Z silianacom-svn $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$only_page = JRequest::getInt('only_page',0);
if($only_page == 1) return;
