<?php
declare(strict_types=1);
namespace KingLiving\GeoBlocking\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Store\Model\Store;

class AddGeoCmsBlock implements DataPatchInterface, PatchRevertableInterface
{
    const CMS_US_IDENTIFIER = 'geo-us-block';
    const CMS_GLOBAL_IDENTIFIER = 'geo-global-block';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $this->blockFactory->create()
            ->setTitle('CMS US Block')
            ->setIdentifier(self::CMS_US_IDENTIFIER)
            ->setIsActive(true)
            ->setContent('<div>Sample CMS Block Content</div>')
            ->setStores([Store::DEFAULT_STORE_ID])
            ->save();

        $this->blockFactory->create()
            ->setTitle('CMS GLOBAL Block')
            ->setIdentifier(self::CMS_GLOBAL_IDENTIFIER)
            ->setIsActive(true)
            ->setContent('<div>Sample CMS Block Content</div>')
            ->setStores([Store::DEFAULT_STORE_ID])
            ->save();

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_US_IDENTIFIER, 'identifier');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }

        $sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_GLOBAL_IDENTIFIER, 'identifier');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }

    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
