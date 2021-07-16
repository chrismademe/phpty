<?php

namespace Staple;

use Staple\Render\PHP;
use Staple\Render\Twig;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Filesystem\Filesystem;

class Builder {

    public function __construct(Staple $instance) {
        $this->instance = $instance;
        $this->config = $instance->config;
    }

    public function build() {
        Console::info( 'Starting build...', '🤖' );
        $startedAt = microtime(true);

        $this->clearOutputDir();
        $this->createOutputDir();
        $input = $this->locateInputFiles();
        $input = $this->populateData($input);
        $input = $this->generateCollections($input);
        $output = $this->renderTemplates($input);
        $this->writeOutputToFilesystem($output);
        $this->copyPassthroughFiles();

        $finishedAt = microtime(true);

        Console::info( sprintf(
            'Built site in %s seconds',
            $finishedAt - $startedAt
        ), '🤖' );
    }

    /**
     * Create Output Directory
     */
    public function createOutputDir() {
        mkdir($this->config->outputDir);
    }

    /**
     * Clear Output Directory
     */
    public function clearOutputDir() {
        $this->deleteFiles($this->config->outputDir);
    }

    /**
     * Locate Input Files
     * Scans input directory for recognised files
     */
    public function locateInputFiles() {

        $locator = new LocateFiles([
            'dir' => $this->config->inputDir,
            'fileTypes' => $this->instance->filters->apply( 'input.fileTypes', [ 'twig', 'php' ] )
        ]);

        return $locator->locate();
    }

    /**
     * Render Templates
     *
     * @param array $input Array of valid input templates to render
     * @return array Rendered template content and frontmatter/meta data
     */
    public function renderTemplates( array $input ) {
        if ( ! empty($input) ) {
            foreach ( $input as $key => $file ) {
                $startedAt = microtime(true);

                // Skip layouts, includes and data
                if (
                    str_contains( $file['pathName'], '_layouts' ) ||
                    str_contains( $file['pathName'], '_includes' ) ||
                    str_contains( $file['pathName'], '_data' )
                ) {
                    unset($input[$key]);
                    continue;
                }

                switch ( $file['fileName'] ) {

                    // Twig
                    case str_ends_with( $file['fileName'], '.twig' ):
                        $twig = new Twig($this->instance);
                        $input[$key]['rendered'] = $twig->render($file['pathName'], $input[$key]['templateData']->body(), $input[$key]['data']);
                        break;

                    // PHP
                    case str_ends_with( $file['fileName'], '.php' ):
                        $php = new PHP($this->instance);
                        $input[$key]['rendered'] = $php->render($file['pathName'], $input[$key]['templateData']->body(), $input[$key]['data']);
                        break;

                }

                // Log Render time
                $finishedAt = microtime(true);
                Console::info( sprintf(
                    'Rendered %s -> %s in %s seconds',
                    $file['pathName'],
                    $input[$key]['data']['page']['permalink'],
                    $finishedAt - $startedAt
                ), '🤖' );
            }

            return $input;
        }
    }

    /**
     * Write Output to Filesystem
     *
     * @param array $output Output from build method
     */
    protected function writeOutputToFilesystem( array $output ) {
        foreach ( $output as $key => $file ) {
            $path = sprintf( '%s/%s', $this->instance->config->outputDir, $file['data']['page']['permalink'] );
            write_file( $path, $file['rendered'] );
        }
    }

    /**
     * Copy Passthrough Files
     */
    protected function copyPassthroughFiles() {
        $files = $this->instance->config->passthrough ?? [];
        if ( empty($files) ) return;

        $filesystem = new Filesystem;

        foreach ( $files as $file ) {
            $isDirectory = is_dir($file);
            $filename = ltrim( $file, $this->instance->config->inputDir );
            Console::info(sprintf('Copying %s', $file), '📂');

            // Directory
            if ( $isDirectory ) {
                $filesystem->mirror( $file, sprintf('%s%s', $this->instance->config->outputDir, $filename) );
            } else {
                $filesystem->copy( $file, sprintf('%s/%s', $this->instance->config->outputDir, $filename) );
            }
        }
    }

    /**
     * Parse Front Matter
     *
     * Reads front matter and returns syntax valid templates
     * @param string $contents
     * @return object
     */
    public function parseFrontMatter( string $contents ) {
        return YamlFrontMatter::parse($contents);
    }

    public function populateData( array $input ) {
        foreach ( $input as $key => $file ) {
            // Load template contents
            $contents = @file_get_contents($this->config->inputDir . '/' . $file['pathName']);

            // Parse Front Matter
            $input[$key]['templateData'] = $this->parseFrontMatter($contents);

            // Populate data
            $input[$key]['data'] = $this->populateDefaultData([
                'page' => array_merge( $input[$key]['templateData']->matter(), $file )
            ] );

            $input[$key]['isCollection'] = array_key_exists( 'collection', $input[$key]['data']['page'] );
        }

        return $input;
    }

    /**
     * Populate Data
     */
    public function populateDefaultData( array $data ) {

        // Generate permalink
        if ( ! array_key_exists( 'permalink', $data['page'] ) ) {
            $permalinkParts = explode('.', $data['page']['pathName']);
            array_pop($permalinkParts);
            $permalinkBare = join('.', $permalinkParts);

            // Index
            if ( $permalinkBare === 'index' ) {
                $data['page']['permalink'] = 'index.html';

            // Filename path (e.g. something.json.php -> something.json)
            } elseif ( str_is_filename($permalinkBare) ) {
                $data['page']['permalink'] = $permalinkBare;

            // Directory path (e.g. something.html -> something/index.html)
            } else {
                $data['page']['permalink'] = sprintf( '%s/index.html', join('.', $permalinkParts) );
            }
        }

        // Add in global data
        $data = array_merge( $this->instance->data->get(), $data );

        return $data;

    }

    /**
     * Generate Collections
     *
     * @param array $input
     */
    public function generateCollections( array $input ) {

        foreach ( $input as $key => $file ) {
            if ( $file['isCollection'] === false ) continue;

            // Remove the collection from the original input array
            unset($input[$key]);

            // Setup Collection
            $collection = $file['data']['page']['collection'];

            // Validate collection data exists
            if ( ! array_key_exists( $collection['data'], $file['data'] ) ) {
                Console::warn( sprintf( 'Couldn\'t locate collection data with key "%s", skipping...', $collection['data'] ) );
                continue;
            }

            // Populate collection as templates
            foreach ( $file['data'][$collection['data']] as $__key => $__value ) {
                $file['data'][$collection['alias']] = $__value;

                // Parse permalink with Twig
                $twig = new Twig($this->instance);
                $file['data']['page']['permalink'] = $twig->render('', $file['data']['page']['collection']['permalink'], $file['data']);

                $input[] = $file;
            }
        }

        return $input;
    }

    /**
     * Delete Files
     * Clear out a target directory
     *
     * @param string $target Directory path
     * @see https://paulund.co.uk/php-delete-directory-and-files-in-directory
     */
    protected function deleteFiles( string $target ) {
        if ( is_dir($target) ) {
            $files = glob( $target . '/*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

            foreach( $files as $file ) {
                $this->deleteFiles( $file );
            }

            rmdir( $target );
        } elseif ( is_file($target) ) {
            unlink( $target );
        }
    }

}