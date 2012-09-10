<?php

/*
 * This file is part of the easybook application.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Jazzyweb\doDocBundle\Services;;

/**
 * Groups several validators used across the application.
 */
class EasyBookValidator
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Validates that the given $value is not an empty string.
     */
    public static function validateNonEmptyString($name, $value)
    {
        if (null == $value || '' == trim($value)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: The $name cannot be empty.");
        }

        return $value;
    }

    public static function validateDirExistsAndWritable($dir)
    {
        if (null == $dir || '' == trim($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: The directory cannot be empty.");
        }

        if (!is_dir($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: '$dir' directory doesn't exist.");
        }

        if (!is_writable($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: '$dir' directory is not writable.");
        }

        return $dir;
    }

    /**
     * Validates that the given $slug is a valid string for a book slug.
     */
    public static function validateBookSlug($slug)
    {
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $slug)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException(
                "ERROR: The slug can only contain letters, numbers and dashes (no spaces)"
            );
        }

        return $slug;
    }

    /**
     * Validates that the book represented by the given $slug exists in $dir directory.
     */
    public function validateBookDir($slug, $baseDir)
    {
        $attempts = 6;
        $bookDir  = $baseDir.'/'.$slug;

        // check that the given book already exists or ask for another slug
        while (!file_exists($bookDir) && $attempts--) {
            if (!$attempts) {
                throw new \RuntimeException(sprintf(
                    " ERROR: Too many failed attempts. Check that your book is in\n"
                    ." '%s/' directory",
                    realpath($baseDir)
                ));
            }

            throw new \RuntimeException(sprintf("The given slug doesn't match any book"));

            $bookDir = $baseDir.'/'.$slug;
        }

        return $bookDir;
    }

    /**
     * Validates that the given book configuration is valid.
     */
    public function validateBookConfig($config)
    {
        // TODO: validate configuration against some sort of schema instead of
        // the current trivial check
        if (!array_key_exists('title', $config)) {
            throw new \RuntimeException(sprintf(
                "Malformed 'config.yml' configuration file for '%s' book \n\n"
                ."Open '%s' file\n"
                ."and add at least the 'title' configuration option ",
                $this->app->get('publishing.book.slug'),
                realpath($this->app->get('publishing.dir.book')).'/config.yml'
            ));
        }
    }

    /**
     * Validates that the given $slug is a valid string for a edition slug.
     */
    public static function validateEditionSlug($slug)
    {
        if (!preg_match('/^[a-zA-Z0-9\-\_]+$/', $slug)) {
            throw new \InvalidArgumentException(
                "ERROR: The edition name can only contain letters, numbers and dashes (no spaces)"
            );
        }

        return $slug;
    }

    /**
     * Validates that the given edition configuration is valid.
     */
    public function validateEditionConfig($config)
    {
        // TODO: validate configuration against some sort of schema
    }

    /**
     * Validates that the given $edition is defined in the book configuration.
     */
    public function validatePublishingEdition($edition)
    {
        $attemps = 6;
        $bookDir = $this->app->get('publishing.dir.book');

        // if book defines no edition, raise an exception
        if (count($this->app->book('editions') ?: array()) == 0) {
            throw new \RuntimeException(sprintf(
                " ERROR: Book hasn't defined any edition.\n"
                ."\n"
                ." Check that your book has at least one edition defined under\n"
                ." 'editions' option in the following configuration file:\n"
                ."\n"
                ." '%s'",
                realpath($this->app->get('publishing.dir.book').'/config.yml')
            ));
        }

        // check that the book has defined the given edition or ask for another edition
        while (!array_key_exists($edition, $this->app->book('editions')) && $attemps--) {
            if (!$attemps) {
                throw new \RuntimeException(sprintf(
                    " ERROR: Too many failed attempts. Check that your book has a\n"
                    ." '%s' edition defined in the following configuration file:\n"
                    ." '%s'",
                    $edition, realpath($bookDir.'/config.yml')
                ));
            }
            
            throw new \RuntimeException(sprintf("the edition %s isn't defined for '%s' book",
                    $edition, $this->app->book('title') ));
                        
        }

        return $edition;
    }

    /**
     * Performs the last global validation of the final resolved book configuration
     */
    public function validateResolvedConfiguration()
    {
        // TODO
    }
}
