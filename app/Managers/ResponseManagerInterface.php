<?php

namespace App\Managers;

use Symfony\Component\HttpFoundation\Response;

interface ResponseManagerInterface
{
    const INCLUDES_ATTRIBUTE = 'includes';
    const EXCLUDES_ATTRIBUTE = 'excludes';

    /**
     * Sets model manager.
     *
     * @param ModelManagerInterface $modelManager
     *
     * @return self
     */
    public function setModelManager(ModelManagerInterface $modelManager);

    /**
     * Parse items to include in the response.
     *
     * @param string $includes The list of item names to include (separated using coma).
     *
     * @return void
     */
    public function parseIncludes(string $includes);

    /**
     * Parse items to exclude in the response.
     *
     * @param string $excludes The list of item names to exclude (separated using coma).
     *
     * @return void
     */
    public function parseExcludes(string $excludes);

    /**
     * Returns response for one item.
     *
     * @param array $headers
     * @param int   $options
     *
     * @return Response
     */
    public function item(array $headers = [], int $options = 0);

    /**
     * Returns response for a collection of items.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    public function collection(array $headers = [], int $options = 0);

    /**
     * Returns response for a paginated collection of items.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    public function paginate(array $headers = [], int $options = 0);

    /**
     * Returns a response based on the given data.
     *
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return Response
     */
    public function response(array $data = null, int $status = Response::HTTP_OK, array $headers = [], int $options = 0);

    /**
     * Returns a response based on the given list of errors.
     *
     * @param array $errors
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return Response
     */
    public function errors(array $errors, int $status = Response::HTTP_BAD_REQUEST, array $headers = [], int $options = 0);
}
