<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AIOSEO\Vendor\Monolog\Handler;

use AIOSEO\Vendor\Monolog\Logger;
use AIOSEO\Vendor\Monolog\Formatter\NormalizerFormatter;
use AIOSEO\Vendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \AIOSEO\Vendor\Monolog\Handler\AbstractProcessingHandler
{
    private $client;
    public function __construct(\AIOSEO\Vendor\Doctrine\CouchDB\CouchDBClient $client, $level = \AIOSEO\Vendor\Monolog\Logger::DEBUG, $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter()
    {
        return new \AIOSEO\Vendor\Monolog\Formatter\NormalizerFormatter();
    }
}
