# Base RESTful API Lumen
This project is a base to build a RESTful API based on [Lumen](1).

## Configuration
The base of the application is designed to handle the most common use cases. But to make it works with your models you
will have to configure a few things. To avoid any structure modification you can change the application behaviour by 
adding public properties directly on your models:

* **repository (string):** Defines the class of the repository for the given model.
* **transformer (string):** Defines the class of the transformer for the given model.
* **key (string):** Defines the key used as a namespace in the generated responses for the given model.
* **primaryKey (string):** Defines the name of the primary key attribute for the given model.
* **rules (array):** Defines the name of the validation rules attributes for the given model.
* **limit (int):** Defines the number of elements per page in the paginated response for the given model.

These properties are really useful however if it does not cover your use case you can extend the application structure
by using the given interfaces:

* `App\Managers\ModelManagerInterface`: Coordinate other interfaces to manage models.
* `App\Managers\ResponseManagerInterface`: Handle response generation.
* `App\Repositories\ModelRepositoryInterface`: Handle database actions.
* `App\Transformers\ModelTransformerInterface`: Handle model transformation for response generation.

## Versioning
The application MUST be maintained under the Semantic Versioning guidelines so releases will be numbered with the 
following format: `MAJOR.MINOR.PATCH`

For more information on SemVer, please visit [semver.org](3)

## Routing
All routes MUST be prefixed by the version of the API they are included in.
The base of the application handle 5 routes by default: index, show, store, update and destroy. If you want more routes
you can easily extends the application logic.

* **index (GET):** `/<version>/<modelName>/`. Return a paginated list for the given model.
* **show (GET):** `/<version>/<modelName>/{primaryKey}`. Return a single item for the given model.
* **store (POST):** `/<version>/<modelName>/{primaryKey}`. Store and return a new item for the given model.
* **update (PUT):** `/<version>/<modelName>/{primaryKey}`. Update and return an existing item for the given model.
* **destroy (DELETE):** `/<version>/<modelName>/{primaryKey}`. Delete and return an existing item for the given model.

`version` is the API version for the given route.  
`modelName` MUST be the plural of the model's name. 

## Validation
The base application structure allows to create multiple validation rules sets for a given model by defining the public
property `rules` as an array as `[$set => (array) $rules]`.
To use a specific rules set to validate an array of inputs for a managed model you can use the model manager like:

```php
$rulesSet = 'store' // Name of the rules set to use
$modelManager->validate($inputs, $rulesSet);
```

If the managed model does not define the given rules set, the `default` will be used.
If the managed model does not define the `default` rules set, an exception will be thrown.

## Responses
All responses must follow the [JSON-API (v1.0)](2) standard. By default every response generated by this project respect
this requirement, except errors which are following the structure described in [Exceptions and Errors](#exceptions-and-errors) section.

## Exceptions and Errors
All errors are represented as PHP exceptions but there are 2 different types with different purposes.

* **ExceptionInterface:** defines errors created by developers because of a code problem.
* **ErrorExceptionInterface:** defines errors occurred because of the user action (undefined endpoint, validation fail, etc).

### ExceptionInterface
All mechanisms implemented in this project throw exceptions if the code does not respect structure requirement.
These exceptions must implement `App\Exceptions\ExceptionInterface` and are handled by the default Lumen exceptions handler logic.

### ErrorExceptionInterface
When an error is directly related to an user action the thrown exception must implement `App\Exceptions\ErrorExceptionInterface`.
These exceptions are handled by the response manager to return a JSON response to the user with all error's details as following:

* **status:** HTTP status code applicable to this error, expressed as a string value.
* **code:** Application-specific error code, expressed as a string value.
* **title:** Short human-readable summary of the error. It SHOULD NOT change from occurrence to occurrence of the error, except for purposes of localization.
* **details:** Human-readable explanation specific to this occurrence of the error.
* **href (optional):** URI that MAY yield further details about this particular occurrence of the error.
* **path (optional):** Relative path to the relevant attribute within the associated resource(s). Only appropriate for errors that apply to a single resource or type of resource.
* **links (optional):** Associated resources, which can be dereferenced from the request document.

## Implementation
To implement a new model in the application you need to:

1. Create the model object under `App\Models` namespace
2. Create a new controller under `App\Controllers\<version>` and set the `model` property to the model's fully qualified class name (FQCN)
3. Create default routes pointing to the newly created controller

And that's it!

If all the configuration on the model object does not cover your use case you can extends the model manager 
functionalities by creating a new one which implements `App\Managers\ModelManagerInterface` and override the controller's
constructor to pass it.

You can also extends to response manager functionalities by creating a new one which implements 
`App\Managers\ResponseManagerInterface` and override the controller's constructor to pass it.

[1]: https://lumen.laravel.com/
[2]: http://jsonapi.org/
[3]: http://semver.org
