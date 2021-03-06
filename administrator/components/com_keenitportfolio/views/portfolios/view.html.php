<?php

/**
 * @version     2.0.0
 * @package     com_keenitportfolio
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Keenitportfolio.
 */
class KeenitportfolioViewPortfolios extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        KeenitportfolioHelper::addSubmenu('portfolios');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/keenitportfolio.php';

        $state = $this->get('State');
        $canDo = KeenitportfolioHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_KEENITPORTFOLIO_TITLE_PORTFOLIOS'), 'portfolios.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/portfolio';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('portfolio.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('portfolio.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('portfolios.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('portfolios.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'portfolios.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('portfolios.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('portfolios.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'portfolios.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('portfolios.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_keenitportfolio');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_keenitportfolio&view=portfolios');

        $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(
			JText::_("JOPTION_SELECT_CATEGORY"),
			'filter_category',
			JHtml::_('select.options', JHtml::_('category.options', 'com_keenitportfolio'), "value", "text", $this->state->get('filter.category'))

		);

		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

    }

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.project_name' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_PROJECT_NAME'),
		'a.client_name' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_CLIENT_NAME'),
		'a.final_date' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_FINAL_DATE'),
		'a.project_url' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_PROJECT_URL'),
		'a.category' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_CATEGORY'),
		'a.image' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_IMAGE'),
		'a.desc' => JText::_('COM_KEENITPORTFOLIO_PORTFOLIOS_DESC'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		);
	}

}
