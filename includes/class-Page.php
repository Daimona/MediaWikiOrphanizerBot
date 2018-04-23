<?php
# it.wiki deletion bot in PHP
# Copyright (C) 2018 Valerio Bozzolan
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program. If not, see <https://www.gnu.org/licenses/>.

namespace itwikidelbot;

use \wm\WikipediaIt;
use \mw\Tokens;

/**
 * Handle a page
 */
class Page {

	/**
	 * Do nothing
	 *
	 * @var bool
	 */
	public static $PORCELAIN = false;

	/**
	 * Debug mode
	 *
	 * @var bool
	 */
	public static $INSPECT = false;

	/**
	 * @var string Page title with prefix
	 */
	private $title;

	/**
	 * Construct a Page
	 *
	 * @param $title Page title with its prefix
	 */
	public function __construct( $title ) {
		$this->title = $title;
	}

	/**
	 * Get the page title with its prefix
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Save this page
	 *
	 * @param $content string Page content
	 * @param $summary string Edit summary
	 * @return bool|mixed False if not created
	 */
	public function saveByContentSummary( $content, $summary ) {
		return static::saveByTitleContentSummary( $this->getTitle(), $content, $summary );
	}

	/**
	 * Save this page if it does not exist
	 *
	 * @param $content string Page content
	 * @param $summary string Edit summary
	 * @return bool|mixed False if not created
	 */
	public function saveByContentSummaryIfNotExists( $content, $summary ) {
		return self::saveByTitleContentSummaryIfNotExists( $this->getTitle(), $content, $summary );
	}

	/**
	 * Check if this page exists
	 *
	 * @return bool
	 */
	public function exists() {
		return static::existsByTitle( $this->getTitle() );
	}

	/**
	 * Save a page
	 *
	 * @param $title string Page name with prefix
	 * @param $content string Page content
	 * @param $summary string Edit summary
	 * @return mixed Response
	 */
	public static function saveByTitleContentSummary( $title, $content, $summary ) {
		$api = self::api();
		$args = [
			'action'  => 'edit',
			'title'   => $title,
			'text'    => $content,
			'summary' => $summary,
			'token'   => $api->login()->getToken( Tokens::CSRF ),
			'bot'     => 1,
		];
		if( self::$INSPECT ) {
			print_r( $args );
			$handle = fopen( 'php://stdin', 'r' );
			$line = fgets( $handle );
			fclose( $handle );
		}
		if( ! self::$PORCELAIN ) {
			return $api->post( $args );
		}
	}

	/**
	 * Save a page if it does not exist
	 *
	 * @param $title string Page name with prefix
	 * @param $content string Page content
	 * @param $summary string Edit summary
	 * @return bool|mixed False if not created
	 */
	public static function saveByTitleContentSummaryIfNotExists( $title, $content, $summary ) {
		if( ! static::existsByTitle( $title ) ) {
			return static::saveByTitleContentSummary( $title, $content, $summary );
		}
		return false;
	}

	/**
	 * Check if a page title exists
	 *
	 * @param $title string Page name with prefix
	 * @return bool
	 */
	public static function existsByTitle( $title ) {
		$result = self::api()->fetch( [
			'action' => 'query',
			'prop'   => 'info',
			'titles' => $title,
		] );
		foreach( $result->query->pages as $pageid => $page ) {
			return ! isset( $page->missing );
		}
		return false;
	}

	/**
	 * Get the API related to this page
	 *
	 * @return mw\API
	 */
	protected static function api() {
		return WikipediaIt::getInstance();
	}
}