# AjaxHandler
Handle AJAX requests anywhere in your PHP. Just add `ajaxRequest=true` (configurable in the class) to your GET, POST, PUT or DELETE request data. If it's not there, subsequent calls to AjaxHandler methods will not do anything, but a call to `error` or `success` will send the response and exit the script. If your framework requires post processing after your main scripts this is probably a problem and I'd be interested in any ideas for dealing with that. 

Based on https://github.com/serkanyersen/AjaxHandler but stripped down and designed to be used anywhere in your code.

## Example
```php
/* Get beers */
$ajaxHandler = new AjaxHandler($_GET);

$style = $_GET['style'];
$beerList = new BeerList();

try {
    $beers = $beerList->getByStyle($style);
} catch(Exception $e) {
    $ajaxHandler->error($e->getMessage(), 500);
    throw $e; // won't get here if it is an AJAX request
}

$ajaxRequest->success('Got '.count($beers).' beers', array('data' => $beers));

display('templates/beers.tpl', $beers); // won't get here if it's an AJAX request 
```

The `error` call will send an HTTP 500 response with the XHR responseText being the error message. 

The `success` call will send a json encoded response like this:

```javascript
{
    success: true,
    data: [beer, beer, beer, beer],
    message: 'Got n beers'
}
```

## JSONP
You can also get a JSONP response by providing a `callback` function name in your request data. 
