<?php
namespace Vilaspa\UserBundle\Extension;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Vilaspa\UserBundle\Entity\User;

class UserListener {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $lcea) {
        $user = $lcea->getEntity();
        if(!$user instanceof User) {
            return;
        }

        $em = $lcea->getEntityManager();
        //update Point value
        if (!is_null($user->getLatitude()) && !is_null($user->getLongitude())
                && $user->getLatitude() != 0 && $user->getLongitude() != 0) {
            $sql = "UPDATE Users ua
                SET ua.point = PointFromWKB(Point(:lat, :lng))
                WHERE ua.id = :uid";
            $em->getConnection()->executeUpdate($sql, array('uid' => $user->getId(), 'lat' => $user->getLatitude(), 'lng' => $user->getLongitude()));
        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs) {
        $user = $eventArgs->getEntity();
        if(!$user instanceof User) {
            return;
        }

        $em = $eventArgs->getEntityManager();
        //update Point value
        if ($eventArgs->hasChangedField('lastpointupdate') && !is_null($user->getLatitude()) && !is_null($user->getLongitude())
                && $user->getLatitude() != 0 && $user->getLongitude() != 0) {
            $sql = "UPDATE Users ua
                SET ua.point = PointFromWKB(Point(:lat, :lng))
                WHERE ua.id = :uid";
            $em->getConnection()->executeUpdate($sql, array('uid' => $user->getId(), 'lat' => $user->getLatitude(), 'lng' => $user->getLongitude()));
        }
    }
}