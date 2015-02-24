<?php
namespace Magento\Log\Controller\Adminhtml\Online;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DEG_CustomerRegIp::Customerregip');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('customerregip_lookup')->setIpAddress(
            $this->getRequest()->getParam('ip', '')
        );
        $this->_view->renderLayout();

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Customer::customer_online');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customers Now Online'));

        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Online Customers'), __('Online Customers'));

        $this->_view->renderLayout();
    }
}
