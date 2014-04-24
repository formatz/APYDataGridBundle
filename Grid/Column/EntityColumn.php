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
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityColumn extends Column
{
    /**
     * @var string Entity name used in dql queries
     */
    protected $dqlEntityName;

    protected $results;

    public function __initialize(array $params)
    {
        // Disable the filter of the column
        $this->setFilterable(false);
        $this->setOrder(false);

        parent::__initialize($params);

        $this->setDqlEntityName($this->getParam('dqlEntityName'));
    }

    /**
     * @param string $dqlEntityName
     */
    public function setDqlEntityName($dqlEntityName)
    {
        $this->dqlEntityName = $dqlEntityName;
    }

    /**
     * @return string
     */
    public function getDqlEntityName()
    {
        return $this->dqlEntityName;
    }

    public function isQueryValid($query)
    {
        $result = array_filter((array) $query, "is_numeric");

        return !empty($result);
    }

    public function getDisplayedValues($result, $columnId, $manager)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $manager */

        $values = array();
        $columnName = str_replace('.', '::', $columnId);
        $ids= array();
        foreach ($result as $row) {
            $ids[] = $row[$columnName];
        }
        if(empty($ids)) {
            return $ids;
        }
        $fieldName = explode('::', $columnName);
        $fieldName = $fieldName[1];
        $entityClass = $manager->getClassMetadata($this->dqlEntityName);
        $arrFieldId = $entityClass->getIdentifier();
        $accessor = PropertyAccess::createPropertyAccessor();
        /** @var \Doctrine\ORM\Query $query */
        $query = $manager->createQuery('SELECT o FROM ' . $this->dqlEntityName . ' o WHERE o.id IN(' . implode(',', array_map('intval', array_unique($ids))) .')');
        $entities = $query->getResult();
        foreach($entities as $entity) {
            $values[$accessor->getValue($entity, $fieldName)] = $entity->__toString();
        }
        asort($values);

        $this->results = $values;

        return $values;
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
        return $this->results[$value];
    }


    public function getType()
    {
        return 'entity';
    }
}
