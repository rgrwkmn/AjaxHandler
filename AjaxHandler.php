<?php

/**
 * Adapted from https://github.com/serkanyersen/AjaxHandler
 */
class AjaxHandler {

    public $isAjaxRequest = true;
    public $responseContentType = "application/x-json";
    public $callback;

    public function __construct($data)
    {
        if (!$data['ajaxRequest']) {
            $this->isAjaxRequest = false;
            return $this;
        }

        if ($data['responseContentType']) {
            $this->responseContentType = $data['responseContentType'];
        }

        // JSONP
        $this->callback = $data['callback'];
    }

    /**
     * Prompts a standard error response, all errors must prompt by this function
     * adds success:false automatically
     * @param object|string $message An error message, you can directly pass all parameters here
     * @param object $addHash[optional] contains the all error parameters will be sent as a response
     */
    public function error($message, $status = 400)
    {
        if (!$this->isAjaxRequest) {
            return;
        }

        # Prevent browsers to cache response
        @header("Cache-Control: no-cache, must-revalidate", true); # HTTP/1.1
        @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);   # Date in the past
        @header("Content-Type: ".$this->responseContentType."; charset=utf-8", true, $status);

        echo $message;
        exit;
    }

    /**
     * Prompts the request response by given hash
     * adds standard success:true message automatically
     * @param object|string $message Success message you can also pass the all parameters as an array here
     * @param object $addHash [optional] all other parameters to be sent to user as a response
     */
    public function success($message, $addHash = array(), $status = 200)
    {
        if (!$this->isAjaxRequest) {
            return;
        }

        if (is_array($message)) {
            $status = $addHash; // If first argument is addhash then second is the status
            $addHash = $message;
        } else {
            $addHash["message"] = $message;
        }

        $addHash["success"] = true;

        # Prevent browsers to cache response
        @header("Cache-Control: no-cache, must-revalidate", true); # HTTP/1.1
        @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);   # Date in the past
        @header("Content-Type: ".$this->responseContentType."; charset=utf-8", true, $status);

        if ($this->callback) {
            $response = $this->callback."(".json_encode($addHash).");";
        } else {
            $response = json_encode($addHash);
        }

        echo $response;
        exit;
    }
}
