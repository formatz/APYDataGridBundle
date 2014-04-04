<?php

/*
 * This file is part of the DataGridBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 * (c) Stanislav Turza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\DataGridBundle\Grid\Column;

use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Filter;

class EntityColumn extends Column
{
    public function isQueryValid($query)
    {
        $result = array_filter((array) $query, "is_numeric");

        return !empty($result);
    }

    public function getDisplayedValues($values, $columnId, $manager)
    {
        $values = array();
        foreach ($values as $row) {
            $idEntity = $row[str_replace('.', '::', $columnId)];
            $owner = $this->manager->createQuery('SELECT o FROM FormatzTxproBundle:User o WHERE o.id=:id')->setParameter('id',$idEntity)->getSingleResult();
            $values[$idEntity] = $owner->__toString();
        }
        asort($values);

        return $values;
    }

    public function getDisplayedValue($value, $manager) {
        $owner = $this->manager->createQuery('SELECT o FROM FormatzTxproBundle:User o WHERE o.id=:id')->setParameter('id',$idEntity)->getSingleResult();
        return $owner->__toString();
    }


    /**
     * Draw cell
     *
     * @param string $value
     * @param Row $row
     * @param $router
     * @return string
     */
    public function renderCell($value, $row, $router)
    {
        /** @var Row $row */
        return $row->getEntity()->getOwner()->__toString();
        /*$this->values[$row->getEntity()->getOwner()->getId()] = $value;
        return $value;*/
    }


    public function getType()
    {
        return 'entity';
    }
}
