<?php
namespace DEG\CustomerRegIp\Model\Entity\Attribute\Backend;

class Remoteip extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress)
    {
        $this->_remoteAddress = $remoteAddress;
    }

    /**
     * Set the remote IP address on new entities if available
     *
     * @param \Magento\Framework\Object $object
     * @return null
     */
    public function beforeSave($object)
    {
        if (!$object->getId() && is_null($object->getData($this->getAttribute()->getAttributeCode()))) {
            $object->setData($this->getAttribute()->getAttributeCode(), $this->_getRemoteAddr());
        }
        return parent::beforeSave($object);
    }

    /**
     * Return the remote address if available
     *
     * @return string
     */
    protected function _getRemoteAddr()
    {
        try {
            $remoteAddr = $this->_remoteAddress->getRemoteAddress();
        } catch (Exception $e) {
            $remoteAddr = (string)$e->getMessage();
        }
        return $remoteAddr;
    }
}
