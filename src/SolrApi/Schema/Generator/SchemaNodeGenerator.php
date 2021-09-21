<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\Schema\Generator;

use Solrphp\SolariumBundle\Common\Manager\ConfigNode;
use Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema;
use Solrphp\SolariumBundle\SolrApi\Schema\Enum\SubPath;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\CopyField;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\Field;
use Solrphp\SolariumBundle\SolrApi\Schema\Model\FieldType;

/**
 * SchemaNode Generator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SchemaNodeGenerator
{
    /**
     * @param \Solrphp\SolariumBundle\SolrApi\Schema\Config\ManagedSchema $schema
     *
     * @return array<int, ConfigNode>
     */
    public function get(ManagedSchema $schema): array
    {
        $return = [];

        if (\count($schema->getFields())) {
            $return[] = new ConfigNode(Field::class, SubPath::LIST_FIELDS, $schema->getFields());
        }

        if (\count($schema->getDynamicFields())) {
            $return[] = new ConfigNode(Field::class, SubPath::LIST_DYNAMIC_FIELDS, $schema->getDynamicFields());
        }

        if (\count($schema->getCopyFields())) {
            $return[] = new ConfigNode(CopyField::class, SubPath::LIST_COPY_FIELDS, $schema->getCopyFields());
        }

        if (\count($schema->getFieldTypes())) {
            $return[] = new ConfigNode(FieldType::class, SubPath::LIST_FIELD_TYPES, $schema->getFieldTypes());
        }

        return $return;
    }
}
