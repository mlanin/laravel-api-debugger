<?php

return [
	/**
	 * Specify what data to collect.
	 */
	'collections' => [
		// Database queries.
		\Lanin\Laravel\ApiDebugger\Collections\QueriesCollection::class,

		// Profile custom events.
		\Lanin\Laravel\ApiDebugger\Collections\ProfilingCollection::class,

		// Show cache events.
		\Lanin\Laravel\ApiDebugger\Collections\CacheCollection::class,
	],
];