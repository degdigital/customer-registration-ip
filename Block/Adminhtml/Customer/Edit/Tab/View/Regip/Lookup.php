<?php
namespace DEG\CustomerRegIp\Block\Adminhtml\Customer\Edit\Tab\View\Regip;

class Lookup extends \Magento\Backend\Block\Template
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \DEG\CustomerRegIp\Model\Ipinfodb
     */
    protected $_ipinfodb;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \DEG\CustomerRegIp\Model\Ipinfodb $ipinfodb
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \DEG\CustomerRegIp\Model\Ipinfodb $ipinfodb,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_ipinfodb = $ipinfodb;
        parent::__construct($context, $data);
    }

    protected function _beforeToHtml()
    {
        try {
            $info = $this->_ipinfodb->lookupIp($this->getIpAddress());
            $this->setInfo($info);
        } catch (Exception $e) {;
            $this->_logger->logException($e);
            $this->setInfo(new \Magento\Framework\Object(array(
                'country_name' => $this->__('Lookup Error: %s', $e->getMessage())
            )));
        }
        return parent::_beforeToHtml();
    }

    public function resolveDnsAddr($ipAddress = null)
    {
        if (is_null($ipAddress)) {
            $ipAddress = $this->getIpAddress();
        }
        if ('127.0.0.1' == $ipAddress) {
            return 'localhost';
        }
        return gethostbyaddr($ipAddress);
    }
}