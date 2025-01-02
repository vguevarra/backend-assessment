<?php
declare(strict_types=1);
namespace KingLiving\GeoBlocking\Observer\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Data implements ObserverInterface
{
    /**
     * @var SerializerInterface
     */
    const XML_ACCOUNT_ID = 'geo_config/option_group/account_id';
    const XML_LICENSE_KEY = 'geo_config/option_group/license_key';
    const XML_URL_KEY = 'geo_config/option_group/url_key';

    protected $serializer;
    private Curl $curl;
    private ScopeConfigInterface $scopeConfig;
    private Context $context;
    private \Magento\Framework\App\ResponseInterface $response;
    private \Magento\Framework\App\Response\RedirectInterface $redirect;
    private \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private UrlInterface $url;

    public function __construct(
        SerializerInterface $serializer,
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        Context $context,
        UrlInterface $url
    ){
        $this->serializer = $serializer;
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        $this->url = $url;
    }
    public function execute(Observer $observer)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $account_id = $this->scopeConfig->getValue(self::XML_ACCOUNT_ID, $storeScope);
        $license_key = $this->scopeConfig->getValue(self::XML_LICENSE_KEY, $storeScope);
        $url_key = $this->scopeConfig->getValue(self::XML_URL_KEY, $storeScope);

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->setCredentials($account_id, $license_key);
        $this->curl->get($url_key);
        $data = $this->serializer->unserialize($this->curl->getBody());

        if (($data['country']['iso_code'] != 'RUS') || ($data['country']['iso_code'] != 'CHN'))  {
            $product = $observer->getProduct();
            $originalName = $product->getName();
            $modifiedName = $originalName . " - " . $data['country']['iso_code'];
            $product->setName($modifiedName);
            return true;
        } else {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $norouteUrl = $this->url->getUrl('noroute');
            return $resultRedirect->setUrl($norouteUrl);
        }
    }
}
