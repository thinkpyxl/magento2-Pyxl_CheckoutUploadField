<?php
/**
 * Pyxl_CheckoutUploadField
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2018 Pyxl, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Pyxl\CheckoutUploadField\Model;

class ImageUploader extends \Magento\Catalog\Model\ImageUploader
{

    /**
     * Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * List of allowed image mime types
     *
     * @var array
     */
    private $allowedMimeTypes = [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'application/pdf',
        'application/msword', // doc
        'application/octet-stream', // docx
        'application/vnd.ms-excel', //xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //xlsx
    ];

    /**
     * ImageUploader constructor.
     *
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $baseTmpPath
     * @param string $basePath
     * @param array $allowedExtensions
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        string $baseTmpPath,
        string $basePath,
        array $allowedExtensions
    )
    {
        parent::__construct(
            $coreFileStorageDatabase,
            $filesystem,
            $uploaderFactory,
            $storeManager,
            $logger,
            $baseTmpPath,
            $basePath,
            $allowedExtensions
        );
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Checking file for save and save it to tmp dir
     * We copied directly from extended class, but had to do this
     * for it to send the updated $allowedMimeTypes
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        if (!$uploader->checkMimeType($this->allowedMimeTypes)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('File validation failed.'));
        }
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        unset($result['path']);

        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /**
         * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['url'] = $this->storeManager
                             ->getStore()
                             ->getBaseUrl(
                                 \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                             ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }

}