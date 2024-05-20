<?php
declare(strict_types=1);

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\Data\ShipmentInterface;

class GetLabelService
{
    private $file;
    private $httpClientFactory;
    private $configProvider;
    private $fileFactory;
    private $filesystem;

    public function __construct(
        File $file,
        ClientFactory $httpClient,
        ConfigProvider $configProvider,
        FileFactory $fileFactory,
        Filesystem $filesystem
    ) {
        $this->file = $file;
        $this->httpClientFactory = $httpClient;
        $this->configProvider = $configProvider;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * Return array with file path to Inpost Shipping PDF Label
     *
     * @param ShipmentInterface $shipment
     * @return string[]
     * @throws \InPost\Shipment\Service\Http\HttpClientException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getLabel(ShipmentInterface $shipment): array
    {
        // TODO: Remove hardcoded ID
        $inpostShipmentId = $shipment->getInpostShipmentId();

        if (!$inpostShipmentId) {
            $message = __('Shipment does not contain Inpost Shipment ID.');
            throw new \Exception($message->__toString());
        }

        $client = $this->httpClientFactory->create();
        $apiKey = $this->configProvider->getApiKey();
        $merchantId = $this->configProvider->getMerchantId();

        if (!$apiKey || !$merchantId) {
            $message = __('Missing or wrong credentials. Please check InPost Settings.');
            throw new \Exception($message->__toString());
        }

        $client->setAuthToken($apiKey);

        $response = $client->get(
            $this->configProvider->getShipXBaseUrl() . "/v1/shipments/{$inpostShipmentId}/label",
            ["type" => $this->configProvider->getLabelType(), "format" => $this->configProvider->getLabelFormat()]
        );

        return [$this->storeFile($response->getBody(), 'shipment_' . $shipment->getIncrementId())];
    }

    /**
     * Return array with file paths to Inpost Shipping PDF Labels
     *
     * @param AbstractCollection $shipmentCollection
     * @return array
     * @throws \InPost\Shipment\Service\Http\HttpClientException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getLabels(AbstractCollection $shipmentCollection): array
    {
        $labels = [];
        foreach ($shipmentCollection as $shipment) {
            $labels[] = $this->getLabel($shipment)[0];
        }

        return $labels;
    }

    /**
     * @param array $files
     * @return ResponseInterface
     * @throws \Exception
     */
    public function downloadArchive(array $files): ResponseInterface
    {
        if (!count($files)) {
            $message = __("Something went wrong. No labels for download.");
            throw new \Exception($$message->__toSting());
        }

        $zip = new \ZipArchive();
        $zipName = 'labels.zip';

        $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::TMP)->getAbsolutePath();
        $zipPath = $tmpDirectory . $zipName;

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            $message = __('Error during creating ZIP archive.');
            throw new \Exception($message->__toString());
        }

        foreach ($files as $filePath) {
            if (file_exists($filePath)) {
                $parts = explode('/', $filePath);
                $zip->addFile($filePath, 'labels/' . end($parts));
            }
        }

        $zip->close();

        // Delete tmp files
        foreach ($files as $filePath) {
            $this->file->deleteFile($filePath);
        }

        return $this->fileFactory->create(
            $zipName,
            [
                'type' => 'filename',
                'value' => $zipPath,
                'rm' => true,
            ],
            DirectoryList::TMP,
            'application/zip'
        );
    }

    /**
     * @param $fileContent
     * @param $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function storeFile($fileContent, $fileName): string
    {
        $fileName .= '.' .$this->configProvider->getLabelFormat();
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::TMP)->getAbsolutePath();

        if (!$this->file->isDirectory($folderPath)) {
            $this->file->createDirectory($folderPath);
        }

        $filePath = $folderPath . $fileName;
        $this->file->filePutContents($filePath, $fileContent);

        return $filePath;
    }
}
