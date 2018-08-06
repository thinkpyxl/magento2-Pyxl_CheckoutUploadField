<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Pyxl\CheckoutUploadField\Controller\File;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
    protected $jsonSerializer;

    /**
     * @var \Pyxl\CheckoutUploadField\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $baseTmpPath;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Pyxl\CheckoutUploadField\Model\ImageUploader $imageUploader
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Pyxl\CheckoutUploadField\Model\ImageUploader $imageUploader,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultFactory = $context->getResultFactory();
        $this->jsonSerializer = $jsonSerializer;
        $this->imageUploader = $imageUploader;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {

        try {
            $result = $this->imageUploader->saveFileToTmpDir('files');

            // Save to quote
            $quote = $this->checkoutSession->getQuote();
            $quoteAdditionalData = $quote->getAdditionalData() ?: [];

            $this->baseTmpPath = $this->imageUploader->getBaseTmpPath();
            $file_path = $this->baseTmpPath . '/' . $result['file'];

            array_push($quoteAdditionalData, json_encode(['po_filename' => $file_path]));
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
