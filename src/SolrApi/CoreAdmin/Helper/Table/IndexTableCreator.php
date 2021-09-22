<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\SolrApi\CoreAdmin\Helper\Table;

use Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Index Table Creator.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class IndexTableCreator
{
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface                 $output
     * @param \Solrphp\SolariumBundle\SolrApi\CoreAdmin\Response\StatusResponse $response
     *
     * @return \Symfony\Component\Console\Helper\Table
     */
    public function create(OutputInterface $output, StatusResponse $response): Table
    {
        $rows = [];

        foreach ($response->getStatus() as $status) {
            $core = $status->getName();

            if (null !== ($index = $status->getIndex())) {
                $data = $index->jsonSerialize();
                unset($data['userData']);
                $rows[] = array_merge([$core], $data);
            }
        }

        $headers = isset($data) ? array_merge(['core'], array_keys($data)) : [];

        return (new Table($output))
            ->setStyle('box')
            ->setHeaderTitle('index')
            ->setHeaders($headers)
            ->setRows($rows)
        ;
    }
}
