<?php namespace Lanin\ApiDebugger;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Events\Dispatcher as Event;
use Symfony\Component\HttpFoundation\Response;

class Debugger {

	/**
	 * @var Collection
	 */
	private $queries;

	/**
	 * @var Event
	 */
	private $event;

	/**
	 * @var Collection
	 */
	private $debug;

	/**
	 * @var bool
	 */
	private $collectQueries = false;

	/**
	 * Create a new Debugger service.
	 *
	 * @param Event $event
	 */
	public function __construct(Event $event)
	{
	    $this->queries = new Collection();
	    $this->debug   = new Collection();
		$this->event   = $event;

		$this->event->listen('kernel.handled', function($request, $response)
		{
			$this->updateRequest($request, $response);
		});
	}

	/**
	 * Listen database queries events.
	 */
	public function collectDatabaseQueries()
	{
		$this->collectQueries = true;

		$this->event->listen('illuminate.query',function ($query, $args, $time)
		{
			$this->logQuery($query, $args, $time);
		});
	}

	/**
	 * Log DB query.
	 *
	 * @param string $query
	 * @param array $attributes
	 * @param float $time
	 */
	private function logQuery($query, $attributes, $time)
	{
		$query = vsprintf(str_replace(['%', '?'], ['%%', "'%s'"], $query), $attributes);

		$this->queries->push([
			'query' => $query,
			'time' 	=> $time,
		]);
	}

	/**
	 * Add vars to debug output.
	 */
	public function dump()
	{
		foreach (func_get_args() as $var)
		{
			$this->debug->push($var);
		}
	}

	/**
	 * Update final response.
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	private function updateRequest(Request $request, Response $response)
	{
		if ($response instanceof JsonResponse && $this->needToUpdateResponse())
		{
			$data = $response->getData(true);

			if ($this->collectQueries)
			{
				$data['debug']['total_queries'] = $this->queries->count();
				$data['debug']['queries'] = $this->queries;
			}

			if ( ! $this->debug->isEmpty())
			{
				$data['debug']['dump'] = $this->debug;
			}

			$response->setData($data);
		}
	}

	/**
	 * Check if debugger has to update the response.
	 *
	 * @return bool
	 */
	private function needToUpdateResponse()
	{
		return $this->collectQueries || !$this->debug->isEmpty();
	}
}