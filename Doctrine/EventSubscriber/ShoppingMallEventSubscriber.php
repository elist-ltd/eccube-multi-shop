<?php

namespace Plugin\ShoppingMall\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Entity\Delivery;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Request\Context;

class ShoppingMallEventSubscriber implements EventSubscriber
{
    /**
     * @var Context
     */
    private $requestContext;

    /**
     * ShoppingMallEventSubscriber constructor.
     *
     * @param Context $requestContext
     */
    public function __construct(Context $requestContext)
    {
        $this->requestContext = $requestContext;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postLoad,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $this->setShop($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $this->setShop($entity);
    }

    private function setShop($entity)
    {
        $Member = $this->requestContext->getCurrentUser();
        // 管理画面処理
        if ($Member instanceof Member) {
            // ショップメンバー処理
            if ($Member->isShop()) {
                if ($entity instanceof Product) {
                    $entity->setShop($Member->getShop());
                }
                if ($entity instanceof ProductClass) {
                    $entity->setShop($Member->getShop());
                    if ($Member->getShop()->isSaleType()) {
                        $entity->setSaleType($Member->getShop()->getSaleType());
                    }
                }
                if ($entity instanceof ClassCategory) {
                    $entity->setShop($Member->getShop());
                }
                if ($entity instanceof ClassName) {
                    $entity->setShop($Member->getShop());
                }
                if ($entity instanceof Order) {
                    $entity->setShop($Member->getShop());
                }
                if ($entity instanceof Delivery) {
                    $entity->setShop($Member->getShop());
                }
            }
        } else {
            // フロント画面処理
            if ($entity instanceof Order) {
                $Shop = $entity->getShopFromItems();
                $entity->setShop($Shop);
            }
        }
    }
}
