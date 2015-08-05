<?php namespace FHTeam\SelectelStorageApi\Exception;

/**
 * Exception indicating an error during some parallel HTTP operation
 *
 * @package ForumHouse\SelectelStorageApi\Exception
 */
class ParallelOperationException extends AbstractSelectelStorageException
{
    /**
     * @var array Array of error descriptions
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * @param string $operation
     * @param array  $errors
     */
    public function __construct($operation, array $errors)
    {
        $this->errors = $errors;
        parent::__construct(
            sprintf(
                "Error executing operation '[%s]' over at least one file. Error details: '%s'",
                $operation,
                json_encode($errors)
            )
        );
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
