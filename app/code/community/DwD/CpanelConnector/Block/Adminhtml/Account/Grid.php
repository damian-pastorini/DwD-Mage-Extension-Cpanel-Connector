<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Block_Adminhtml_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('accountsGrid');
		$this->setDefaultSort('cpanel_user');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('cpanel_connector/account')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
        $this->addColumn('cpanel_user', array(
            'header'    => Mage::helper('cpanel_connector')->__('cPanel User'),
            'align'     => 'left',
            'index'     => 'cpanel_user',
        ));
        $this->addColumn('magento_user_email', array(
            'header'    => Mage::helper('cpanel_connector')->__('Magento User Email'),
            'align'     => 'left',
            'index'     => 'magento_user_id',
            'renderer'    => 'cpanel_connector/adminhtml_account_edit_form_element_useremail'
        ));
        $this->addColumn('magento_user_id', array(
            'header'    => Mage::helper('cpanel_connector')->__('Magento User ID'),
            'align'     => 'left',
            'index'     => 'magento_user_id',
            'width'     => '120px',
        ));
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('cpanel_connector')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getCpanelUser',
				'actions'   => array(
				array(
					'caption'   => Mage::helper('cpanel_connector')->__('Edit'),
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

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('cpanel_user');
		$this->getMassactionBlock()->setFormFieldName('account');
		$statuses = array(''=>'', 0 => 'Suspended', 1 => 'Enabled');
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('cpanel_connector')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('cpanel_connector')->__('Status'),
					'values' => $statuses
				)
			)
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('cpanel_user' => $row->getId()));
	}

}
