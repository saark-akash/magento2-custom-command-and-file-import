<?php

namespace Akash\MageTest\Model;

class ReadFileRepository implements \Akash\MageTest\Api\ReadFileRepositoryInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    public $fileDriver;
    /**
     * @var \Akash\MageTest\Api\CreateCustomerRespositoryInterface 
     */
    public $createCustomerRespository;
    /**
     * @var \Magento\Framework\Module\Dir 
     */
    public $moduleDir;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;
    /**
     * Constructor
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Module\Dir $moduleDir
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Akash\MageTest\Api\CreateCustomerRespositoryInterface $createCustomerRespository
     */
    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Module\Dir $moduleDir,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Akash\MageTest\Api\CreateCustomerRespositoryInterface $createCustomerRespository
    ) {
        $this->csv = $csv;
        $this->moduleDir = $moduleDir;
        $this->json = $json;
        $this->fileDriver = $fileDriver;
        $this->createCustomerRespository = $createCustomerRespository;
    }
    /**
     * @inheritDoc
     */
    public function readFile($source, $profileName)
    {
        try {
            if ($source == \Akash\MageTest\Console\Command\SomeCommand::SAMPLE_JSON) {
                return $this->readJsonFile($profileName);
            } else {
                return $this->readCsvFile($profileName);
            }
        } catch (\Exception $e) {
            return 3;
        }
    }
    /**
     * Read json file
     * @param string $fileName
     * @return mixed
     */
    public function readJsonFile($fileName)
    {
        $filePath = $this->getModuleDirectoryPath() . "/Import/" . $fileName;
        if (
            $this->fileDriver->isExists($filePath) &&
            $this->fileDriver->isFile($filePath) &&
            $this->fileDriver->isReadable(
                $filePath
            )
        ) {
            $existCustomer = [];
            $fileContents = $this->fileDriver->fileGetContents($filePath);
            $jsonToArrayContent = $this->json->unserialize($fileContents);
            if (is_array($jsonToArrayContent)) {
                foreach ($jsonToArrayContent as $key => $value) {
                    if ($this->createCustomerRespository->checkCustomerFromEmail($value['emailaddress'])) {
                       $this->createCustomerRespository->createCustomer($value);
                    } else {
                        $existCustomer[] = $value['emailaddress'];
                    } 
                }
            }
            if (!empty($existCustomer)) {
                return $this->json->serialize($existCustomer);
            }
            return 1;
        } else {
            return 0;
        }
    }
    /**
     * Read csv file
     * @param string $fileName
     * @return mixed
     */
    public function readCsvFile($fileName)
    {
        $filePath = $this->getModuleDirectoryPath() . "/Import/" . $fileName;
        if (
            $this->fileDriver->isExists($filePath) &&
            $this->fileDriver->isFile($filePath) &&
            $this->fileDriver->isReadable(
                $filePath
            )
        ) {
            $data = $this->csv->getData($filePath);
            unset($data[0]);
            $existCustomer = [];
            if (is_array($data)) {
                foreach ($data as $value) {
                    if ($this->createCustomerRespository->checkCustomerFromEmail($value['2'])) {
                        $customerArr = [];
                        $customerArr['fname'] = $value['0'];
                        $customerArr['lname'] = $value['1'];
                        $customerArr['emailaddress'] = $value['2'];
                        $this->createCustomerRespository->createCustomer($customerArr);
                    } else {
                        $existCustomer[] = $value['2'];
                    }                   
                }
            }
            if (!empty($existCustomer)) {
                return $this->json->serialize($existCustomer);
            }
            return 1;
        } else {
            return 0;
        }
    }
    /**
     * Get module directory path
     * @return string
     */
    public function getModuleDirectoryPath()
    {
        return $this->moduleDir->getDir('Akash_MageTest');
    }
}