<?php

namespace PHPty;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LocateFiles {

    public function __construct( array $options ) {
        $this->options = $options;
    }

    public function locate() {
        $iterator = new RecursiveDirectoryIterator($this->options['dir']);
        $foundFiles = new RecursiveIteratorIterator($iterator);
        $fileTypes = $this->options['fileTypes'] ?? null;

        foreach ( $foundFiles as $file ) {
            $fileParts = explode( '.', $file );
            $fileExt = array_pop( $fileParts );
            $fileSlug = strtolower( $fileExt );

            if ( $fileTypes && in_array( $fileSlug, $fileTypes) ) {
                $files[] = [
                    'pathName' => str_replace($this->options['dir'] . '/', '', $file->getPathName()),
                    'fileName' => $file->getFileName()
                ];
            }
        }

        return $files ?? null;
    }

}