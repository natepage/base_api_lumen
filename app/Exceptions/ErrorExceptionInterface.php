<?php

namespace App\Exceptions;

interface ErrorExceptionInterface extends ExceptionInterface
{
    /**
     * Set status.
     * HTTP Status associate to this error.
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus(string $status);

    /**
     * Get status.
     * HTTP Status associate to this error.
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set internal code.
     * Application specific error code.
     *
     * @param string $code
     *
     * @return self
     */
    public function setInternalCode(string $code);

    /**
     * Get internal code.
     * Application specific error code.
     *
     * @return string
     */
    public function getInternalCode();

    /**
     * Set title.
     * Short human-readable summary of the error.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title);

    /**
     * Get title.
     * Short human-readable summary of the error.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set details.
     * Human-readable explanation specific to this occurrence of the error.
     *
     * @param string $details
     *
     * @return self
     */
    public function setDetails(string $details);

    /**
     * Get details.
     * Human-readable explanation specific to this occurrence of the error.
     *
     * @return string
     */
    public function getDetails();

    /**
     * Set href.
     * URI that MAY yield further details about this particular occurrence of the error.
     *
     * @param string $href
     *
     * @return self
     */
    public function setHref(string $href);

    /**
     * Get href.
     * URI that MAY yield further details about this particular occurrence of the error.
     *
     * @return string
     */
    public function getHref();

    /**
     * Set links.
     * Associated resources, which can be dereferenced from the request document.
     *
     * @param array $links
     *
     * @return self
     */
    public function setLinks(array $links);

    /**
     * Get links.
     * Associated resources, which can be dereferenced from the request document.
     *
     * @return array
     */
    public function getLinks();

    /**
     * Set path.
     * Relative path to the relevant attribute within the associated resource(s).
     * Only appropriate for errors that apply to a single resource or type of resource.
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path);

    /**
     * Get path.
     * Relative path to the relevant attribute within the associated resource(s).
     * Only appropriate for errors that apply to a single resource or type of resource.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set meta.
     * Additional data if needed in really specific case.
     *
     * @param array $meta
     *
     * @return self
     */
    public function setMeta(array $meta);

    /**
     * Get meta.
     * Additional data if needed in really specific case.
     *
     * @return array
     */
    public function getMeta();

    /**
     * Get array representation of the error.
     *
     * @return array
     *
     * @throws BaseException If a required property is missing
     */
    public function toArray();
}
