<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the DnaGifts Component
 */
class DnaGiftsViewDnaGifts extends JView
{
	public function display($tpl = null) 
	{
		$model = $this->getModel();
		$this->assignRef( 'autoSuggestData', $model->getAutoSuggestData() );
		
		require_once JPATH_COMPONENT.'/helpers/dnagifts.php';
		
		$app		= JFactory::getApplication();
		$params    	= $app->getParams();
		$dispatcher	= JDispatcher::getInstance();
		
		// Get some data from the models
		$state		= $this->get('State');
		$this->form	= $this->get('Form');
		$this->item = $this->get('Item');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Display the template
		//parent::display($tpl);
		
		$result = $this->loadTemplate($tpl);
		if ($result instanceof Exception)
		{
			return $result;
		}

		echo $result;
		
		// Set the document
		$this->setDocument();
	}
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		
		/**
		 * NOTE: Only add stylesheets and scripts here that are applicable ONLY to
		 * 		this page. If it is needed on multiple pages add it to <base>/dnagifts.php
		**/
		// Stylesheets
		$document->addStyleSheet(JURI::base(true).'/components/com_dnagifts/css/dnagifts.css');
		
		// Javascripts
		// - JQuery - UI
		// -- Core
		$document->addScript(JURI::base(true).'/administrator/components/com_dnagifts/js/ui/jquery.ui.core.min.js');
		$document->addScript(JURI::base(true).'/administrator/components/com_dnagifts/js/ui/jquery.ui.widget.min.js');
		$document->addScript(JURI::base(true).'/administrator/components/com_dnagifts/js/ui/jquery.ui.position.min.js');
		
		// -- Interactions
		
		// -- Widgets
		$document->addScript(JURI::base(true).'/administrator/components/com_dnagifts/js/ui/jquery.ui.autocomplete.min.js');
		$document->addScript(JURI::base(true).'/administrator/components/com_dnagifts/js/ui/jquery.ui.menu.min.js');
		
		// - Other
		
		// - DNA Gifts
		$document->addScript(JURI::base(true).'/components/com_dnagifts/js/dnagifts.js');
	}
}