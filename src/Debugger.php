<?php

namespace Lanin\Laravel\ApiDebugger;

use Illuminate\Events\Dispatcher as Event;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lanin\Laravel\ApiDebugger\Events\StopProfiling;
use Lanin\Laravel\ApiDebugger\Events\StartProfiling;
use Symfony\Component\HttpFoundation\Response;

class Debugger
{
    /**
     * @var string
     */
    protected $responseKey = 'debug';

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
     * @return mixed
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
     * @param  \Closure|null $action
     * @return mixed
     */
    public function profileMe($name, \Closure $action = null)
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
        if ($this->needToUpdateResponse($response)) {
            $data = $response->getData(true) ?: [];
            $data[$this->responseKey] = $this->storage->getData();

            $response->setData($data);
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
        return $response instanceof JsonResponse && ! $this->storage->isEmpty();
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
