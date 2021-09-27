<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\Common\Serializer\Data;

/**
 * Prepare Callable.
 *
 * prepares solr response data.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PrepareCallable
{
    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|mixed
     */
    public function prepareSolrResponse(array $data)
    {
        return $this->normalize($data);
    }

    /**
     * making sure solr responses like "last-component" or "first-component"
     * can be mapped using the camel case naming strategy.
     *
     * as for instance the config values are passed as "first_component" and
     * the naming strategy modifies the property's serialized name rather than
     * the data key, it's impossible to provide a naming strategy which matches
     * both variations.
     *
     * another consideration would be virtual properties as shorthand but that
     * would require code change every time solr decides to put a dash in a response key.
     *
     * @param array<string, mixed>|mixed $data
     *
     * @return array<string, mixed>|mixed
     */
    private function normalize($data)
    {
        if (!\is_array($data)) {
            return $data;
        }

        $ret = [];

        foreach ($data as $k => $v) {
            $key = preg_replace('/(?<=[a-z])(?=[A-Z])/', '_', str_replace('-', '_', $k)) ?? $k;
            $ret[strtolower($key)] = $this->normalize($v);
        }

        return $ret;
    }
}
