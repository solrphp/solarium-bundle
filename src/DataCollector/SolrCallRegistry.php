<?php

declare(strict_types=1);

/*
 * This file is part of the SolrPHP SolariumBundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solrphp\SolariumBundle\DataCollector;

use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;
use Solrphp\SolariumBundle\Common\Util\StringUtil;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * Solr Call Registry.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SolrCallRegistry
{
    private const FORMAT_JSON = 'json';
    private const FORMAT_XML = 'xml';

    /**
     * @var array<int,array<string, mixed>>
     */
    private array $calls = [];

    /**
     * @return array<int,array<string, mixed>>
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * @param \Solarium\Core\Event\PreExecuteRequest $event
     */
    public function addRequest(PreExecuteRequest $event): void
    {
        $request = $event->getRequest();
        $format = \is_string($request->getParam('wt')) ? $request->getParam('wt') : self::FORMAT_JSON;

        $data = [
            'resource' => sprintf('%s%s', $event->getEndpoint()->getBaseUri(), $request->getHandler()),
            'request_headers' => new InputBag($request->getHeaders()),
            'request_options' => new InputBag($request->getOptions()),
            'request_params' => new InputBag(HeaderUtils::parseQuery(substr($request->getUri() ?? '', strpos($request->getUri() ?? '', '?') + 1))),
            'request_body' => $this->prettify($request->getRawData(), $format),
            'response_headers' => '',
            'response_body' => '',
            'start' => microtime(true),
        ];

        $this->calls[spl_object_id($request)] = $data;
    }

    /**
     * @param \Solarium\Core\Event\PostExecuteRequest $event
     */
    public function addResponse(PostExecuteRequest $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $format = \is_string($request->getParam('wt')) ? $request->getParam('wt') : self::FORMAT_JSON;

        $id = spl_object_id($request);

        $this->calls[$id]['end'] = microtime(true);
        $this->calls[$id]['response_headers'] = new InputBag($response->getHeaders());
        $this->calls[$id]['response_body'] = $this->prettify($response->getBody(), $format);
        $this->calls[$id]['status_code'] = $response->getStatusCode();
        $this->calls[$id]['duration'] = ($this->calls[$id]['end'] - (isset($this->calls[$id]['start']) ? $this->calls[$id]['start'] : $this->calls[$id]['end']));
    }

    /**
     * @param string|null $data
     * @param string      $format
     *
     * @return string
     */
    private function prettify(?string $data, string $format): string
    {
        if (str_ends_with($format, self::FORMAT_XML)) {
            return StringUtil::prettyXML($data);
        }

        if (self::FORMAT_JSON === $format) {
            return StringUtil::prettyJson($data);
        }

        return $data ?? '';
    }
}
