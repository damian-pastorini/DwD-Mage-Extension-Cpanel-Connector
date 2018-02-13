<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2017 DwDeveloper (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

/**
 * Class DwD_CpanelConnector_Block_Adminhtml_Account_Grid
 */
class DwD_CpanelConnector_Block_Adminhtml_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * DwD_CpanelConnector_Block_Adminhtml_Account_Grid constructor.
     */
	public function __construct()
	{
		parent::__construct();
		$this->setId('accountsGrid');
		$this->setDefaultSort('cpanel_user');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getAccountModel()
    {
        return Mage::getModel('cpanel_connector/account');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
	{
		$collection = $this->getAccountModel()->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

    /**
     * @return $this
     */
	protected function _prepareColumns()
	{
        $this->addColumn('cpanel_user', array(
            'header'    => $this->__('cPanel User'),
            'align'     => 'left',
            'index'     => 'cpanel_user',
        ));
        $this->addColumn('magento_user_email', array(
            'header'    => $this->__('Magento User Email'),
            'align'     => 'left',
            'index'     => 'magento_user_id',
            'renderer'    => 'cpanel_connector/adminhtml_account_edit_form_element_useremail'
        ));
        $this->addColumn('magento_user_id', array(
            'header'    => $this->__('Magento User ID'),
            'align'     => 'left',
            'index'     => 'magento_user_id',
            'width'     => '120px',
        ));
		$this->addColumn('action',
			array(
				'header'    =>  $this->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getCpanelUser',
				'actions'   => array(
				array(
					'caption'   => $this->__('Edit'),
					'url'       => array('base'=> '*/*/edit'),
					'field'     => 'cpanel_user'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));
		return parent::_prepareColumns();
	}

    /**
     * @return $this
     */
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('cpanel_user');
		$this->getMassactionBlock()->setFormFieldName('account');
		$statuses = array('' => '', 0 => 'Suspended', 1 => 'Enabled');
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> $this->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => $this->__('Status'),
					'values' => $statuses
				)
			)
		));
		return $this;
	}

    /**
     * @param $row
     * @return string
     */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('cpanel_user' => $row->getId()));
	}

}
