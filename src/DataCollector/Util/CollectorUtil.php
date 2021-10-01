<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\DataCollector\Util;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solrphp\SolariumBundle\Common\Util\StringUtil;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * CollectorUtil.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class CollectorUtil
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param \Solarium\Core\Client\Request  $request
     * @param \Solarium\Core\Client\Endpoint $endpoint
     *
     * @return array<string, float|string|InputBag>
     */
    public static function fromRequest(Request $request, Endpoint $endpoint): array
    {
        $format = \is_string($request->getParam('wt')) ? $request->getParam('wt') : StringUtil::FORMAT_JSON;

        return [
            'start' => microtime(true),
            'resource' => sprintf('%s%s', $endpoint->getBaseUri(), $request->getHandler()),
            'request_headers' => new InputBag($request->getHeaders()),
            'request_options' => new InputBag($request->getOptions()),
            'request_params' => new InputBag(HeaderUtils::parseQuery(substr($request->getUri() ?? '', strpos($request->getUri() ?? '', '?') + 1))),
            'request_body' => StringUtil::prettify($request->getRawData(), $format),
            'response_headers' => '',
            'response_body' => '',
            'status_code' => '',
        ];
    }

    /**
     * @param \Solarium\Core\Client\Request  $request
     * @param \Solarium\Core\Client\Response $response
     *
     * @return array<string, float|string|int|InputBag>
     */
    public static function fromResponse(Request $request, Response $response): array
    {
        $format = \is_string($request->getParam('wt')) ? $request->getParam('wt') : StringUtil::FORMAT_JSON;

        return [
            'end' => microtime(true),
            'response_headers' => new InputBag($response->getHeaders()),
            'response_body' => StringUtil::prettify($response->getBody(), $format),
            'status_code' => $response->getStatusCode(),
        ];
    }
}
