<?php

namespace Webfactory\Doctrine\Config;

/**
 * Specifies a connection to a file-based SQLite database.
 */
class FileDatabaseConnectionConfiguration extends ConnectionConfiguration
{
    /**
     * @param string|null $filePath
     */
    public function __construct($filePath = null)
    {
        parent::__construct(array(
            'driver'   => 'pdo_sqlite',
            'user'     => 'root',
            'password' => '',
            'path'     => $this->toFilePath($filePath)
        ));
    }

    /**
     * Returns the path to the database file.
     *
     * The database file may not exist.
     *
     * @return \SplFileInfo
     */
    public function getDatabaseFile()
    {
        $parameters = $this->getConnectionParameters();
        return new \SplFileInfo($parameters['path']);
    }

    /**
     * Removes the database file if it exists.
     *
     * @return $this Provides a fluent interface.
     */
    public function cleanUp()
    {
        if ($this->getDatabaseFile()->isFile()) {
            unlink($this->getDatabaseFile()->getPathname());
        }
        return $this;
    }

    /**
     * Returns a file path for the database file.
     *
     * Generates a unique file name if the given $filePath is null.
     *
     * @param string|null $filePath
     * @return string
     */
    private function toFilePath($filePath)
    {
        if ($filePath === null) {
            $temporaryFile =  sys_get_temp_dir() . '/' . uniqid('db-', true) . '.sqlite';
            // Ensure that the temporary file is removed on shutdown, otherwise the filesystem
            // might be cluttered with database files.
            register_shutdown_function(array($this, 'cleanUp'));
            return $temporaryFile;
        }
        return $filePath;
    }
}