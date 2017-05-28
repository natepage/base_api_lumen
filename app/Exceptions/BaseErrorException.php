<?php

namespace App\Exceptions;

class BaseErrorException extends BaseException implements ExceptionInterface
{
    /** @var string */
    protected $status;

    /** @var string */
    protected $internalCode;

    /** @var string */
    protected $title;

    /** @var string */
    protected $details;

    /** @var string */
    protected $href;

    /** @var array */
    protected $links;

    /** @var string */
    protected $path;

    /** @var array */
    protected $meta;

    /** @var array */
    protected $required = [
        'internalCode',
        'title',
        'details'
    ];

    /**
     * Set status.
     * HTTP Status associate to this error.
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     * HTTP Status associate to this error.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set internal code.
     * Application specific error code.
     *
     * @param string $internalCode
     *
     * @return self
     */
    public function setInternalCode(string $internalCode)
    {
        $this->internalCode = $internalCode;

        return $this;
    }

    /**
     * Get internal code.
     * Application specific error code.
     *
     * @return string
     */
    public function getInternalCode()
    {
        return $this->internalCode;
    }

    /**
     * Set title.
     * Short human-readable summary of the error.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        $this->message = $title;

        return $this;
    }

    /**
     * Get title.
     * Short human-readable summary of the error.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set details.
     * Human-readable explanation specific to this occurrence of the error.
     *
     * @param string $details
     *
     * @return self
     */
    public function setDetails(string $details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details.
     * Human-readable explanation specific to this occurrence of the error.
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set href.
     * URI that MAY yield further details about this particular occurrence of the error.
     *
     * @param string $href
     *
     * @return self
     */
    public function setHref(string $href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Get href.
     * URI that MAY yield further details about this particular occurrence of the error.
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set links.
     * Associated resources, which can be dereferenced from the request document.
     *
     * @param array $links
     *
     * @return self
     */
    public function setLinks(array $links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get links.
     * Associated resources, which can be dereferenced from the request document.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set path.
     * Relative path to the relevant attribute within the associated resource(s).
     * Only appropriate for errors that apply to a single resource or type of resource.
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     * Relative path to the relevant attribute within the associated resource(s).
     * Only appropriate for errors that apply to a single resource or type of resource.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set meta.
     * Additional data if needed in really specific case.
     *
     * @param array $meta
     *
     * @return self
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta.
     * Additional data if needed in really specific case.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get array representation of the error.
     *
     * @return array
     *
     * @throws \Exception If a required property is missing
     */
    public function toArray()
    {
        $error = [];

        foreach (get_object_vars($this) as $prop => $value) {
            if (null !== $value) {
                $prop = $prop == 'internalCode' ? 'code' : $prop;
                $error[$prop] = $value;
            } elseif(in_array($prop, $this->required)) {
                throw new \Exception(sprintf('Property %s required on %s.', $prop, __CLASS__));
            }
        }

        return $error;
    }
}
