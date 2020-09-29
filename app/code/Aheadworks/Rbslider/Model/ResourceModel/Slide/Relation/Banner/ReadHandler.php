<?php
namespace Aheadworks\Rbslider\Model\ResourceModel\Slide\Relation\Banner;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\Rbslider\Model\ResourceModel\Slide\Relation\Banner
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(SlideInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_rbslider_slide_banner'), ['banner_id'])
                ->where('slide_id = :id');
            $bannerIds = $connection->fetchCol($select, ['id' => $entityId]);
            $entity->setBannerIds($bannerIds);
        }
        return $entity;
    }
}
