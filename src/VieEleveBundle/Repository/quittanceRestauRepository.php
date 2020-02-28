<?php

namespace VieEleveBundle\Repository;

/**
 * quittanceRestauRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class quittanceRestauRepository extends \Doctrine\ORM\EntityRepository
{
    public  function  findBySimulation($id
    )
    {
        $q=$this->getEntityManager()->createQuery(
            "select A from VieEleveBundle:quittanceRestau A 
        where A.eleve_restau = :id "
        )
            ->setParameter('id',$id);
        return $q->getResult();
    }



}