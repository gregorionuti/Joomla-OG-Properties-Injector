<?php
/**
 * 
 * @version             See field version manifest file
 * @package             See field name manifest file
 * @author				Gregorio Nuti
 * @copyright			See field copyright manifest file
 * @license             GNU General Public License version 2 or later
 * 
 */

// no direct access
defined('_JEXEC') or die;

// Namespaces
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// Import library dependencies
jimport('joomla.plugin.plugin');

/**
 * 
 * This plugin IS NOT PLUG AND PLAY but it should be customised to match your needs.
 * Check out the code below and replace the variables.
 * TODO: make this plugin user friendly by creating fields to select each paramenter from the settings.
 * 
 * 
 */

class plgSystemOg_Properties_Injector extends \Joomla\CMS\Plugin\CMSPlugin 
{
	public function onAfterDispatch()
	{
		
		// Human defined variables - VARIABLES TO BE REPLACED
		$website_url = 'https://www.PLACEHOLDER.com/';
		$facebook_app_id = 'FACEBOOK_APP_ID';
		$default_og_image = 'banners/default-og-image.jpg';
		$apple_icon_path = $apple_icon_path;
		$apple_icon_name = 'apple-icon';
		
		// Check if the app is admin or site
		if (!\Joomla\CMS\Factory::getApplication()->isClient('site')) return;
		
		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
		{
		 	
		}

		// General variables
		$app = Factory::getApplication();
		$document = Factory::getDocument();	
		$input = Factory::getApplication()->input;	
		
		// Add general og properties
		if ($facebook_app_id) {
        	$document->addCustomTag('<meta property="fb:app_id" content="FACEBOOK_APP_ID"/>');
        }
		$document->addCustomTag('<meta property="og:url" content="'.Uri::getInstance().'"/>');
		$document->addCustomTag('<meta property="og:type" content="website"/>');
		$document->addCustomTag('<meta property="og:title" content="'.$document->getTitle().'"/>');
		$document->addCustomTag('<meta property="og:description" content="'.$document->getMetaData("description").'"/>');
		
		// Add og properties to com_content pages
		if ($input->get('option') == 'com_content' && $input->get('view') == 'article') { // Add og properties to articles
				$article_id = Factory::getApplication()->input->get('id');
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
				->select($db->quoteName('images'))
				->from($db->quoteName('#__content'))
				->where('id = '. $db->Quote($article_id));
				$db->setQuery($query);
				$result = $db->loadResult();
				$intro_image = json_decode($result)->image_intro;
				$full_image = json_decode($result)->image_fulltext;
				if ($full_image) {
					$document->addCustomTag('<meta property="og:image" content="'.$website_url.$full_image.'"/>');
				} else if ($intro_image) {
					$document->addCustomTag('<meta property="og:image" content="'.$website_url.$intro_image.'"/>');
				} else {
					$document->addCustomTag('<meta property="og:image" content="'.$website_url.'images/'.$default_og_image.'"/>');
				}
		} else if ($input->get('option') == 'com_content' && $input->get('view') == 'category') { // Add og properties to categories
				$category_id = Factory::getApplication()->input->get('id');
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
				->select($db->quoteName('params'))
				->from($db->quoteName('#__categories'))
				->where('id = '. $db->Quote($category_id));
				$db->setQuery($query);
				$result = $db->loadResult();
				$image = json_decode($result)->image;
				if ($image) {
					$document->addCustomTag('<meta property="og:image" content="'.$website_url.$image.'"/>');
				} else {
					$document->addCustomTag('<meta property="og:image" content="'.$website_url.'images/'.$default_og_image.'"/>');
				}
		} else {
				$document->addCustomTag('<meta property="og:image" content="'.$website_url.'images/'.$default_og_image.'"/>');
		}
		
		/* Add (Apple) icons */
		$document->addHeadLink( $apple_icon_path.$apple_icon_name.'-180.png', 'apple-touch-icon', 'rel', array('sizes'=>'180x180'));
		$document->addHeadLink( $apple_icon_path.$apple_icon_name.'-114.png', 'apple-touch-icon', 'rel', array('sizes'=>'114x114'));
		$document->addHeadLink( $apple_icon_path.$apple_icon_name.'-72.png', 'apple-touch-icon', 'rel', array('sizes'=>'72x72'));
		$document->addHeadLink( $apple_icon_path.$apple_icon_name.'.png', 'apple-touch-icon', 'rel');
		
	}			
	
}
?>