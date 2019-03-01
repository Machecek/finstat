<?php

declare(strict_types=1);

namespace Hubipe\FinStat\Request\Autocomplete;

use Consistence\ObjectPrototype;
use Hubipe\FinStat\Enum\HttpMethod;
use Hubipe\FinStat\Enum\ResponseFormat;
use Hubipe\FinStat\Enum\ServiceUrl;
use Hubipe\FinStat\Exception\InvalidArgumentException;
use Hubipe\FinStat\Parser\Autocomplete\AutocompleteParser;
use Hubipe\FinStat\Parser\IParser;
use Hubipe\FinStat\Request\IRequest;
use Hubipe\FinStat\Utils\HashCalculator;
use Nette\Utils\Strings;

class AutocompleteRequest extends ObjectPrototype implements IRequest
{

	/** @var string */
	private $query;

	/** @var string */
	private $apiKey;

	/** @var string */
	private $hash;

	/** @var string|NULL */
	private $stationId;

	/** @var string|NULL */
	private $stationName;

	public function __construct(
		string $query,
		string $apiKey,
		string $privateKey,
		string $stationId,
		string $stationName)
	{
		$query = Strings::trim($query);
		if (Strings::length($query) < 3) {
			throw new InvalidArgumentException(sprintf('Query has to be at least 3 characters long, "%s" given.', $query));
		}

		$this->query = $query;
		$this->apiKey = $apiKey;
		$this->hash = HashCalculator::getHash($apiKey, $privateKey, $query);
		$this->stationId = $stationId;
		$this->stationName = $stationName;
	}

	public function getMethod(): HttpMethod
	{
		$method = HttpMethod::get(HttpMethod::POST);
		return $method;
	}

	public function getServiceUrl(): ServiceUrl
	{
		$serviceUrl = ServiceUrl::get(ServiceUrl::AUTOCOMPLETE);
		return $serviceUrl;
	}

	public function getData(): array
	{
		$data = [
			'Query' => $this->query,
			'apiKey' => $this->apiKey,
			'Hash' => $this->hash,
		];
		if ($this->stationId !== NULL) {
			$data['StationId'] = $this->stationId;
		}
		if ($this->stationName !== NULL) {
			$data['StationName'] = $this->stationName;
		}

		return $data;
	}

	public function getFormat(): ResponseFormat
	{
		$format = ResponseFormat::get(ResponseFormat::JSON);
		return $format;
	}

	public function getParser(): IParser
	{
		$parser = new AutocompleteParser();
		return $parser;
	}


}
