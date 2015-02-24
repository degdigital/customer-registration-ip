<?php
namespace DEG\CustomerRegIp\Block\Adminhtml\Customer\Edit\Tab\View;

class Regip extends \Magento\Backend\Block\Template
{

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Log\Model\CustomerFactory $logFactory
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        array $data = [])
    {
        $this->customerBuilder = $customerBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if (!$this->customer) {
            $customerData = $this->_backendSession->getCustomerData()['account'];
            $this->customer = $this->customerBuilder->populateWithArray(
                $customerData
            )->create();
        }
        return $this->customer;
    }

    /**
     * Return true if the customer was created in the admin store view
     *
     * @return bool
     */
    public function isCustomerCreatedInAdmin()
    {
        return $this->getCustomer()->getStoreId() == 0;
    }

    public function getCustomerRegIp()
    {
        $remoteAddr = $this->getCustomer()->getCustomAttribute('registration_remote_ip')->getValue();
        // DEBUG:
        // $remoteAddr = dns_get_record('google.com', DNS_A); $remoteAddr = $remoteAddr[0]['ip'];
        return $remoteAddr;
    }

    /**
     * Return the customer registration ip
     *
     * @return string
     */
    public function getCustomerRegIpHtml()
    {
        $remoteAddr = $this->getCustomerRegIp();
        if (!$this->isValidIp()) {
            $html = $this->__('- REGISTRATION IP UNAVAILABLE -');
        } else {
            $html = sprintf('%s', $remoteAddr);
        }
        return $html;
    }

    /**
     *
     * @return bool
     */
    public function isValidIp()
    {
        $remoteAddr = $this->getCustomerRegIp();
        return !empty($remoteAddr);
    }

    /**
     *
     * @return string
     */
    public function getAjaxLookupUrl()
    {
        return $this->getUrl('customerregip',array('ip' => $this->getCustomerRegIp()));
    }

    /**
     * Hide block if the customer hasn't been saved yet
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCustomer() || !$this->getCustomer()->getId()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     *
     * @return bool
     */
    public function isIpInfoDbEnabled()
    {
        $key = $this->_scopeConfig->getValue(
            'customerregip/general/ipinfodb_api_key'
        );

        return (bool)trim($key);
    }
}