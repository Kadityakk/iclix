<?php

namespace App\Support\Imdb;

use RuntimeException;

/**
 * Thrown when IMDb data cannot be fetched: no API key, a network error, or an unknown title.
 */
class ImdbUnavailableException extends RuntimeException {}
