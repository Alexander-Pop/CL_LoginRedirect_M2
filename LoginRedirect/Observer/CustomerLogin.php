<?php
/**
 * GLORY TO UKRAINE! GLORY TO HEROES!
 * @category    CL
 * @package     CL_LoginRedirect
 * @copyright   Copyright (c) 2019-2021 Codelegacy Co., Ltd. All rights reserved. (http://www.codelegacy.com/)
 */

namespace CL\LoginRedirect\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Zend\Validator\Uri;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CustomerLogin implements ObserverInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Uri Validator
     *
     * @var \Zend\Validator\Uri
     */
    protected $uri;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Zend\Validator\Uri $uri
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Uri $uri,
        ResponseFactory $responseFactory
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->uri             = $uri;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handler for 'customer_login' event.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $redirectDashboard = $this->scopeConfig->isSetFlag(
            'customer/startup/redirect_dashboard',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
        );

        // if the Redirect Customer to Account Dashboard after Logging in set to "No"
        if (!$redirectDashboard) {
            $customPage = $this->scopeConfig->getValue(
                'customer/startup/custom_page_for_redirecting',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
            );
            // If the custom page is set and it is a URL valid.
            if (!empty($customPage) && $this->uri->isValid($customPage)) {
                $resultRedirect = $this->responseFactory->create();
                // Redirect to the custom page.
                $resultRedirect->setRedirect($customPage)->sendResponse('200');
                exit();
            }
        }
    }
}
