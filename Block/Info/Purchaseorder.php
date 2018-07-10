<?php
/**
 * @category    Pyxl
 * @package     Pyxl_CheckoutUploadField
 * @copyright   © Pyxl, Inc. All rights reserved.
 * @license     See LICENSE.txt for license details.
 * @author      Justin Rhyne <jrhyne@pyxl.com>
 */

namespace Pyxl\CheckoutUploadField\Block\Info;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class Purchaseorder extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Pyxl_CheckoutUploadField::info/purchaseorder.phtml';

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * Media directory object (readable).
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

	/**
	 * Purchaseorder constructor.
	 *
	 * @param \Magento\Catalog\Model\ImageUploader $imageUploader
	 * @param \Magento\Framework\Filesystem $filesystem
	 * @param Template\Context $context
	 * @param array $data
	 */
    public function __construct(
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Framework\Filesystem $filesystem,
        Template\Context $context,
        array $data = []
    ) {
        $this->imageUploader = $imageUploader;
        $this->mediaDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_OfflinePayments::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }

	/**
	 * Builds a link to the PO File
	 *
	 * @return null|string
	 */
    public function getPurchaseorderFileLink()
    {
    	$link = null;
	    try {
		    if ( $poFilename = $this->getInfo()->getAdditionalInformation( 'po_filename' ) ) {
		    	$link = sprintf(
				    '<a class="file" target="_blank" href="%s">%s</a>',
				    $this->escapeHtml($this->getPurchaseorderFileUrl()),
				    $this->escapeHtml($poFilename)
			    );
		    }
	    } catch ( NoSuchEntityException $e ) {
	    	$this->_logger->error($e);
	    } catch ( LocalizedException $e ) {
	    	$this->_logger->error($e);
	    }

	    return $link;
    }

	/**
	 * Gets the full URL for the PO File
	 *
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
    public function getPurchaseorderFileUrl()
    {
        $mediaPath = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $uploadsPath = $this->imageUploader->getBasePath();
        $filename = $this->getInfo()->getAdditionalInformation('po_filename');

        $filePath = $mediaPath.$uploadsPath.'/'.$filename;
        return $this->getUrl($filePath);
    }

	/**
	 * Added this function to handle exception from getInfo
	 * so the template never fails
	 *
	 * @return string|null
	 */
	public function getPoNumber()
	{
		$poNumber = null;
		try {
			$poNumber = $this->getInfo()->getPoNumber();
		} catch ( LocalizedException $e ) {
			$this->_logger->error($e);
		}
		return $poNumber;
	}

}
