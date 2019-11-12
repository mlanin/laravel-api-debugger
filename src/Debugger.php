<?php

namespace Lanin\Laravel\ApiDebugger;

use Illuminate\Events\Dispatcher as Event;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lanin\Laravel\ApiDebugger\Collections\ProfilingCollection;
use Lanin\Laravel\ApiDebugger\Events\StopProfiling;
use Lanin\Laravel\ApiDebugger\Events\StartProfiling;
use Symfony\Component\HttpFoundation\Response;

class Debugger
{
    /**
     * @var string
     */
    const DEFAULT_RESPONSE_KEY = 'debug';

    /**
     * @var string
     */
    protected $responseKey = self::DEFAULT_RESPONSE_KEY;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var Event
     */
    protected $event;

    /**
     * Create a new Debugger service.
     *
     * @param Storage $storage
     * @param Event $event
     */
    public function __construct(Storage $storage, Event $event)
    {
        $this->storage = $storage;
        $this->event = $event;

        $this->event->listen(RequestHandled::class, function (RequestHandled $event) {
            $this->updateResponse($event->request, $event->response);
        });
    }

    /**
     * Inject custom collection.
     *
     * @param Collection $collection
     */
    public function populateWith(Collection $collection)
    {
        $this->storage->inject($collection);
    }

    /**
     * Add vars to debug output.
     */
    public function dump()
    {
        $this->storage->dump(func_get_args());
    }

    /**
     * Start profiling event.
     *
     * @param  string $name
     */
    public function startProfiling($name)
    {
        $this->event->dispatch(new StartProfiling($name));
    }

    /**
     * Finish profiling event.
     *
     * @param string $name
     */
    public function stopProfiling($name)
    {
        $this->event->dispatch(new StopProfiling($name));
    }

    /**
     * Profile action.
     *
     * @param  string $name
     * @param  \Closure $action
     * @return mixed
     */
    public function profileMe($name, \Closure $action)
    {
        $this->startProfiling($name);
        $return = $action();
        $this->stopProfiling($name);

        return $return;
    }

    /**
     * Update final response.
     *
     * @param Request $request
     * @param Response $response
     */
    protected function updateResponse(Request $request, Response $response)
    {
        $this->stopProfiling(ProfilingCollection::REQUEST_TIMER);

        if ($this->needToUpdateResponse($response)) {
            $data = $this->getResponseData($response);

            if ($data === false || !is_object($data)) {
                return;
            }

            $data->{$this->responseKey} = $this->storage->getData();

            $this->setResponseData($response, $data);
        }
    }

    /**
     * Check if debugger has to update the response.
     *
     * @param  Response $response
     * @return bool
     */
    protected function needToUpdateResponse(Response $response)
    {
        $isJsonResponse = $response instanceof JsonResponse ||
            $response->headers->contains('content-type', 'application/json');

        return $isJsonResponse && !$this->storage->isEmpty();
    }

    /**
     * Fetches the contents of the response and parses them to an assoc array
     *
     * @param Response $response
     * @return object|bool
     */
    protected function getResponseData(Response $response)
    {
        if ($response instanceof JsonResponse) {
            /** @var $response JsonResponse */
            return $response->getData() ?: new \StdClass();
        }

        $content = $response->getContent();

        return json_decode($content) ?: false;
    }

    /**
     * Updates the response content
     *
     * @param Response $response
     * @param object    $data
     * @return JsonResponse|Response
     */
    protected function setResponseData(Response $response, $data)
    {
        if ($response instanceof JsonResponse) {
            /** @var $response JsonResponse */
            return $response->setData($data);
        }

        $content = json_encode($data, JsonResponse::DEFAULT_ENCODING_OPTIONS);

        return $response->setContent($content);
    }

    /**
     * Get the current response key
     *
     * @return string
     */
    public function getResponseKey()
    {
        return $this->responseKey;
    }

    /**
     * Set response attribute key name.
     *
     * @param $key
     */
    public function setResponseKey($key)
    {
        $this->responseKey = $key;
    }
}
