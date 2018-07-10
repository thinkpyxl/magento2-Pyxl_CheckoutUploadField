<?php
/**
 * @category    Pyxl
 * @package     Pyxl_CheckoutUploadField
 * @copyright   © Pyxl, Inc. All rights reserved.
 * @license     See LICENSE.txt for license details.
 * @author      Justin Rhyne <jrhyne@pyxl.com>
 */

namespace Pyxl\CheckoutUploadField\Plugin\OfflinePayments\Model;

use Magento\Catalog\Model\ImageUploader;

class PurchaseorderPlugin
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * PurchaseorderPlugin constructor.
     *
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param \Magento\OfflinePayments\Model\Purchaseorder $subject
     * @param \Magento\Framework\DataObject $data
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeAssignData(
        \Magento\OfflinePayments\Model\Purchaseorder $subject,
        \Magento\Framework\DataObject $data
    ) {

        $po_filename = $data->getAdditionalData('po_filename');
        $po_contact = $data->getAdditionalData('po_contact');

        if ($po_filename) {
            $this->imageUploader->moveFileFromTmp($po_filename);
            $subject->getInfoInstance()->setAdditionalInformation('po_filename', $po_filename);
        }
        if ($po_contact) {
            $subject->getInfoInstance()->setAdditionalInformation('po_contact', $po_contact);
        }
        return null;
    }
}