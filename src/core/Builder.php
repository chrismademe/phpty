<?php

namespace PHPty;

use PHPty\Render\PHP;
use PHPty\Render\Twig;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Filesystem\Filesystem;

class Builder {

    public function __construct(PHPty $instance) {
        $this->instance = $instance;
        $this->config = $instance->config;
    }

    public function build() {
        $startedAt = microtime(true);

        $this->clearOutputDir();
        $this->createOutputDir();
        $input = $this->locateInputFiles();
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
        Console::info('Creating output directory...');
        mkdir($this->config->outputDir);
    }

    /**
     * Clear Output Directory
     */
    public function clearOutputDir() {
        Console::info('Clearing output directory...');
        $this->deleteFiles($this->config->outputDir);
    }

    /**
     * Locate Input Files
     * Scans input directory for recognised files
     */
    public function locateInputFiles() {
        Console::info('Searching for input files...');

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

                // Skip layouts and includes
                if (
                    str_contains( $file['pathName'], '_layouts' ) ||
                    str_contains( $file['pathName'], '_includes' )
                ) {
                    unset($input[$key]);
                    continue;
                }

                // Load template contents
                $contents = @file_get_contents($this->config->inputDir . '/' . $file['pathName']);

                // Parse Front Matter
                $input[$key]['templateData'] = $this->parseFrontMatter($contents);

                // Populate data
                $input[$key]['data'] = $this->populateData([
                    'page' => array_merge( $input[$key]['templateData']->matter(), $file )
                ] );

                switch ( $file['fileName'] ) {

                    // Twig
                    case str_ends_with( $file['fileName'], '.twig' ):
                        $twig = new Twig($this->instance);
                        $input[$key]['rendered'] = $twig->render($input[$key]['templateData']->body(), $input[$key]['data']);
                        break;

                    case str_ends_with( $file['fileName'], '.php' ):
                        $php = new PHP($this->instance);
                        $input[$key]['rendered'] = $php->render($file['pathName'], $input[$key]['data']);
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

    /**
     * Populate Data
     */
    public function populateData( array $data ) {

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

        return $data;

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