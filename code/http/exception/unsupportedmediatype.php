<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Unsupported Media Type Http Exception
 *
 * The server is refusing to service the request because the entity of the request is in a format not supported by the
 * requested resource for the requested method.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Exception\Unsupported
 */
class HttpExceptionUnsupportedMediaType extends HttpExceptionAbstract
{
    protected $code = HttpResponse::UNSUPPORTED_MEDIA_TYPE;
}