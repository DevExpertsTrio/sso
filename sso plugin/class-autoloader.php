<?php
/**
 * @package embed-sharepoint-onedrive-documents
 * @author miniOrange
 * @link https://plugins.miniorange.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define( 'BPC_SSO_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'BPC_SSO_PLUGIN_FILE', __FILE__ );

moazure_include_file( BPC_SSO_PLUGIN_DIR . 'Frontend' );
moazure_include_file( BPC_SSO_PLUGIN_DIR . 'Helper' );
moazure_include_file( BPC_SSO_PLUGIN_DIR . 'includes' );
// moazure_include_file( BPC_SSO_PLUGIN_DIR . 'Controller' );
// moazure_include_file( BPC_SSO_PLUGIN_DIR . 'Wrappers' );
// moazure_include_file( BPC_SSO_PLUGIN_DIR . 'Observer' );
// moazure_include_file( BPC_SSO_PLUGIN_DIR . 'View' );
// moazure_include_file( BPC_SSO_PLUGIN_DIR . 'API' );

/**
 * Traverse all sub-directories for files.
 *
 * Get all files in a directory.
 *
 * @param string $folder Folder to Traverse.
 * @param Array  $results Array of files to append to.
 * @return Array $results Array of files found.
 **/
function moazure_get_dir_contents( $folder, &$results = array() ) {
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder, RecursiveDirectoryIterator::KEY_AS_PATHNAME ), RecursiveIteratorIterator::CHILD_FIRST ) as $file => $info ) {
		if ( $info->isFile() && $info->isReadable() ) {
			$results[ $file ] = realpath( $info->getPathname() );
		}
	}
	return $results;
}

/**
 * Order all php files.
 *
 * Get all php files to require() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return Array Array of php files to require.
 **/
function moazure_get_sorted_files( $folder ) {
	$filepaths  = moazure_get_dir_contents( $folder );
	$interfaces = array();
	$classes    = array();

	foreach ( $filepaths as $file => $filepath ) {
		if ( strpos( $filepath, '.php' ) !== false ) {
				$classes[ $file ] = $filepath;
		}
	}

	return array(
		'classes' => $classes,
	);
}

/**
 * Wrapper for require_all().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return void
 **/
function moazure_include_file( $folder ) {
	if ( ! is_dir( $folder ) ) {
		return;
	}
	$folder   = moazure_sanitize_dir_path( $folder );
	$realpath = realpath( $folder );
	if ( false !== $realpath && ! is_dir( $folder ) ) {
		return;
	}
	$sorted_elements = moazure_get_sorted_files( $folder );
	moazure_require_all( $sorted_elements['classes'] );
}

/**
 * All files given as input are passed to require_once().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param Array $filepaths array of files to require.
 * @return void
 **/
function moazure_require_all( $filepaths ) {
	foreach ( $filepaths as $file => $filepath ) {
		require_once $filepath;
	}
}

/**
 * Validate file paths
 *
 * File names passed are validated to be as required
 *
 * @param string $filename filepath to validate.
 * @return bool validity of file.
 **/
function moazure_is_valid_file( $filename ) {
	return '' !== $filename && '.' !== $filename && '..' !== $filename;
}

/**
 * Function to sanitize dir paths.
 *
 * @param string $folder Dir Path to sanitize.
 *
 * @return string sanitize path.
 */
function moazure_sanitize_dir_path( $folder ) {
	return str_replace( '/', DIRECTORY_SEPARATOR, $folder );
}
